<?php

namespace Drupal\dworkflow\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\dworkflow\WorkflowListInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a quick edit form for workflow lists.
 */
class QuickEditWorkflowForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a QuickEditWorkflowForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dworkflow_quick_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, WorkflowListInterface $workflow_list = NULL) {
    if (!$workflow_list) {
      $this->messenger()->addError($this->t('Workflow list not found.'));
      return $form;
    }

    $form_state->set('workflow_list', $workflow_list);

    $form['info'] = [
      '#type' => 'item',
      '#markup' => $this->t('<h3>Quick Edit: @label</h3><p>Rapidly modify assignments and resource tags without the full edit form.</p>', [
        '@label' => $workflow_list->label(),
      ]),
    ];

    // Check if Group module is available
    $group_module_exists = \Drupal::moduleHandler()->moduleExists('group');

    // Get current assignments
    $current_entities = $workflow_list->getAssignedEntities();
    
    // Build form elements for each entity type
    $form['assigned_entities'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Assigned Users and Groups'),
      '#prefix' => '<div id="assigned-entities-wrapper">',
      '#suffix' => '</div>',
    ];

    $num_entities = $form_state->get('num_entities');
    if ($num_entities === NULL) {
      $count = !empty($current_entities) ? count($current_entities) : 1;
      $form_state->set('num_entities', $count);
      $num_entities = $count;
    }

    for ($i = 0; $i < $num_entities; $i++) {
      $default_type = isset($current_entities[$i]['entity_type']) ? $current_entities[$i]['entity_type'] : 'user';
      $default_entity = isset($current_entities[$i]['entity']) ? $current_entities[$i]['entity'] : NULL;

      $form['assigned_entities'][$i] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['container-inline', 'clearfix']],
      ];

      $form['assigned_entities'][$i]['target_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Type'),
        '#options' => $this->getEntityTypeOptions($group_module_exists),
        '#default_value' => $default_type,
        '#ajax' => [
          'callback' => '::updateEntityAutocomplete',
          'wrapper' => "entity-autocomplete-{$i}",
        ],
      ];

      $selected_type = $form_state->getValue(['assigned_entities', $i, 'target_type']) ?: $default_type;

      $form['assigned_entities'][$i]['target_id'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Select'),
        '#title_display' => 'invisible',
        '#target_type' => $selected_type,
        '#default_value' => $default_entity,
        '#prefix' => "<div id='entity-autocomplete-{$i}'>",
        '#suffix' => '</div>',
      ];
    }

    $form['assigned_entities']['actions'] = [
      '#type' => 'actions',
    ];

    $form['assigned_entities']['actions']['add_entity'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add another'),
      '#submit' => ['::addEntityCallback'],
      '#ajax' => [
        'callback' => '::addEntityAjax',
        'wrapper' => 'assigned-entities-wrapper',
      ],
      '#limit_validation_errors' => [],
    ];

    if ($num_entities > 1) {
      $form['assigned_entities']['actions']['remove_entity'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove last'),
        '#submit' => ['::removeEntityCallback'],
        '#ajax' => [
          'callback' => '::addEntityAjax',
          'wrapper' => 'assigned-entities-wrapper',
        ],
        '#limit_validation_errors' => [],
      ];
    }

    // Resource Location Tags
    $config = \Drupal::config('dworkflow.settings');
    $vocabulary = $config->get('resource_vocabulary') ?: 'resource_locations';

    $form['resource_tags'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Resource Location Tags'),
      '#target_type' => 'taxonomy_term',
      '#tags' => TRUE,
      '#selection_settings' => [
        'target_bundles' => [$vocabulary],
      ],
      '#default_value' => $this->loadTerms($workflow_list->getResourceTags()),
      '#description' => $this->t('Modify resource location tags.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update Workflow'),
      '#button_type' => 'primary',
    ];

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => $workflow_list->toUrl('collection'),
      '#attributes' => ['class' => ['button']],
    ];

    return $form;
  }

  /**
   * Gets entity type options.
   */
  protected function getEntityTypeOptions($include_groups = FALSE) {
    $options = ['user' => $this->t('User')];
    if ($include_groups) {
      $options['group'] = $this->t('Group');
    }
    return $options;
  }

  /**
   * AJAX callback to update entity autocomplete.
   */
  public function updateEntityAutocomplete(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $index = $triggering_element['#parents'][1];
    return $form['assigned_entities'][$index]['target_id'];
  }

  /**
   * Submit callback to add another entity.
   */
  public function addEntityCallback(array &$form, FormStateInterface $form_state) {
    $num_entities = $form_state->get('num_entities');
    $form_state->set('num_entities', $num_entities + 1);
    $form_state->setRebuild();
  }

  /**
   * Submit callback to remove last entity.
   */
  public function removeEntityCallback(array &$form, FormStateInterface $form_state) {
    $num_entities = $form_state->get('num_entities');
    if ($num_entities > 1) {
      $form_state->set('num_entities', $num_entities - 1);
    }
    $form_state->setRebuild();
  }

  /**
   * AJAX callback for add/remove buttons.
   */
  public function addEntityAjax(array &$form, FormStateInterface $form_state) {
    return $form['assigned_entities'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\dworkflow\WorkflowListInterface $workflow_list */
    $workflow_list = $form_state->get('workflow_list');

    // Process assigned entities
    $assigned_entities = [];
    $entities_value = $form_state->getValue('assigned_entities');
    
    if (!empty($entities_value)) {
      foreach ($entities_value as $key => $item) {
        if (!is_numeric($key)) {
          continue;
        }
        
        if (empty($item['target_id']) || empty($item['target_type'])) {
          continue;
        }
        
        $assigned_entities[] = [
          'target_type' => $item['target_type'],
          'target_id' => $item['target_id'],
        ];
      }
    }
    
    $workflow_list->setAssignedEntities($assigned_entities);

    // Process resource tags
    $tags_value = $form_state->getValue('resource_tags');
    $resource_tags = [];
    
    if (!empty($tags_value)) {
      foreach ($tags_value as $item) {
        if (isset($item['target_id'])) {
          $resource_tags[] = $item['target_id'];
        }
      }
    }
    
    $workflow_list->setResourceTags($resource_tags);

    $workflow_list->save();

    $this->messenger()->addMessage($this->t('Workflow list %label has been updated.', [
      '%label' => $workflow_list->label(),
    ]));

    $form_state->setRedirectUrl($workflow_list->toUrl('collection'));
  }

  /**
   * Loads taxonomy terms by IDs.
   *
   * @param array $ids
   *   Array of term IDs.
   *
   * @return array
   *   Array of loaded term entities.
   */
  protected function loadTerms(array $ids) {
    if (empty($ids)) {
      return [];
    }
    
    return $this->entityTypeManager->getStorage('taxonomy_term')->loadMultiple($ids);
  }

}
