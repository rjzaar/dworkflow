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

    // Get current assignments
    $current_entities = $workflow_list->getAssignedEntities();
    $default_entities = [];
    
    foreach ($current_entities as $item) {
      $default_entities[] = $item['entity'];
    }

    // Check if Group module is available
    $group_module_exists = \Drupal::moduleHandler()->moduleExists('group');

    $form['entities'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Assigned Users and Groups'),
      '#target_type' => 'user',
      '#tags' => TRUE,
      '#default_value' => $default_entities,
      '#description' => $this->t('Modify users and groups assigned to this workflow.'),
    ];

    if ($group_module_exists) {
      $form['entities']['#description'] = $this->t('Modify users and groups assigned to this workflow. Type "user:" or "group:" to specify the type.');
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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\dworkflow\WorkflowListInterface $workflow_list */
    $workflow_list = $form_state->get('workflow_list');

    // Process assigned entities
    $entities_value = $form_state->getValue('entities');
    $assigned_entities = [];
    
    if (!empty($entities_value)) {
      foreach ($entities_value as $item) {
        if (isset($item['target_id'])) {
          // Try to load as user first
          $entity = $this->entityTypeManager->getStorage('user')->load($item['target_id']);
          $entity_type = 'user';
          
          // If not a user, try group
          if (!$entity && \Drupal::moduleHandler()->moduleExists('group')) {
            $entity = $this->entityTypeManager->getStorage('group')->load($item['target_id']);
            $entity_type = 'group';
          }
          
          if ($entity) {
            $assigned_entities[] = [
              'target_type' => $entity_type,
              'target_id' => $item['target_id'],
            ];
          }
        }
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
