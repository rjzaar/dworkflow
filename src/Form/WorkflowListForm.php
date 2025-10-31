<?php

namespace Drupal\dworkflow\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the Workflow List add and edit forms.
 */
class WorkflowListForm extends EntityForm {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a WorkflowListForm object.
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
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\dworkflow\WorkflowListInterface $workflow_list */
    $workflow_list = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $workflow_list->label(),
      '#description' => $this->t('Name of the workflow list.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $workflow_list->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dworkflow\Entity\WorkflowList::load',
      ],
      '#disabled' => !$workflow_list->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $workflow_list->getDescription(),
      '#description' => $this->t('Optional description of this workflow list.'),
      '#rows' => 3,
    ];

    // Assigned Entities (Users and/or Groups) - Using Dynamic Entity Reference
    // Check if Group module is available
    $group_module_exists = \Drupal::moduleHandler()->moduleExists('group');
    
    // Get current assignments and format for dynamic entity reference
    $current_entities = $workflow_list->getAssignedEntities();
    $default_values = [];
    
    foreach ($current_entities as $item) {
      $default_values[] = [
        'target_type' => $item['entity_type'],
        'target_id' => $item['entity_id'],
      ];
    }

    // Build available entity types
    $available_entity_types = [
      'user' => [
        'label' => $this->t('User'),
        'handler' => 'default',
        'handler_settings' => [
          'filter' => [
            'type' => '_none',
          ],
          'target_bundles' => NULL,
        ],
      ],
    ];
    
    if ($group_module_exists) {
      $available_entity_types['group'] = [
        'label' => $this->t('Group'),
        'handler' => 'default',
        'handler_settings' => [
          'target_bundles' => NULL,
        ],
      ];
    }

    $form['assigned_entities_wrapper'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    // Add multiple entity reference fields dynamically
    $count = !empty($default_values) ? count($default_values) : 0;
    $count = max($count, 1); // At least one field

    // Store in form state how many we have
    $num_entities = $form_state->get('num_entities');
    if ($num_entities === NULL) {
      $form_state->set('num_entities', $count);
      $num_entities = $count;
    }

    $form['assigned_entities'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Assigned Users and Groups'),
      '#description' => $this->t('Assign users and/or groups to this workflow. Click "Add another" to add more assignments.'),
      '#prefix' => '<div id="assigned-entities-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_entities; $i++) {
      $default_type = isset($default_values[$i]['target_type']) ? $default_values[$i]['target_type'] : 'user';
      $default_id = isset($default_values[$i]['target_id']) ? $default_values[$i]['target_id'] : NULL;
      
      $default_entity = NULL;
      if ($default_id && $default_type) {
        $default_entity = $this->entityTypeManager->getStorage($default_type)->load($default_id);
      }

      $form['assigned_entities'][$i] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['container-inline', 'clearfix']],
      ];

      $form['assigned_entities'][$i]['target_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Type'),
        '#options' => $this->getEntityTypeOptions($group_module_exists),
        '#default_value' => $default_type,
        '#required' => FALSE,
        '#ajax' => [
          'callback' => '::updateEntityAutocomplete',
          'wrapper' => "entity-autocomplete-{$i}",
          'event' => 'change',
        ],
      ];

      // Get the selected type (from form state if ajax, otherwise default)
      $selected_type = $form_state->getValue(['assigned_entities', $i, 'target_type']) ?: $default_type;

      $form['assigned_entities'][$i]['target_id'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Select @type', ['@type' => $this->getEntityTypeLabel($selected_type)]),
        '#title_display' => 'invisible',
        '#target_type' => $selected_type,
        '#default_value' => $default_entity,
        '#required' => FALSE,
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
      '#description' => $this->t('Tag this workflow with resource locations (e.g., Google Drive folders, project servers).'),
    ];

    return $form;
  }

  /**
   * Gets entity type options for the select list.
   */
  protected function getEntityTypeOptions($include_groups = FALSE) {
    $options = [
      'user' => $this->t('User'),
    ];
    
    if ($include_groups) {
      $options['group'] = $this->t('Group');
    }
    
    return $options;
  }

  /**
   * Gets label for entity type.
   */
  protected function getEntityTypeLabel($type) {
    $labels = [
      'user' => $this->t('user'),
      'group' => $this->t('group'),
    ];
    
    return isset($labels[$type]) ? $labels[$type] : $type;
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
   * Submit callback to add another entity field.
   */
  public function addEntityCallback(array &$form, FormStateInterface $form_state) {
    $num_entities = $form_state->get('num_entities');
    $form_state->set('num_entities', $num_entities + 1);
    $form_state->setRebuild();
  }

  /**
   * Submit callback to remove last entity field.
   */
  public function removeEntityCallback(array &$form, FormStateInterface $form_state) {
    $num_entities = $form_state->get('num_entities');
    if ($num_entities > 1) {
      $form_state->set('num_entities', $num_entities - 1);
    }
    $form_state->setRebuild();
  }

  /**
   * AJAX callback for add/remove entity buttons.
   */
  public function addEntityAjax(array &$form, FormStateInterface $form_state) {
    return $form['assigned_entities'];
  }

  /**
   * Validates the assigned entities field.
   */
  public function validateAssignedEntities($element, FormStateInterface $form_state) {
    // Custom validation if needed
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\dworkflow\WorkflowListInterface $workflow_list */
    $workflow_list = $this->entity;

    // Process assigned entities from the multi-field widget
    $assigned_entities = [];
    $entities_value = $form_state->getValue('assigned_entities');
    
    if (!empty($entities_value)) {
      foreach ($entities_value as $key => $item) {
        // Skip non-numeric keys (like 'actions')
        if (!is_numeric($key)) {
          continue;
        }
        
        // Skip empty entries
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

    $status = $workflow_list->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Workflow List.', [
          '%label' => $workflow_list->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Workflow List.', [
          '%label' => $workflow_list->label(),
        ]));
    }

    $form_state->setRedirectUrl($workflow_list->toUrl('collection'));
    
    return $status;
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
