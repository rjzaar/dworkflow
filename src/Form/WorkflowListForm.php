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

    // Assigned Entities (Users and/or Groups)
    $form['assigned_entities'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Assigned Users and Groups'),
      '#description' => $this->t('Select users and/or groups to assign to this workflow.'),
    ];

    // Get current assignments
    $current_entities = $workflow_list->getAssignedEntities();
    $default_entities = [];
    
    foreach ($current_entities as $item) {
      $default_entities[] = $item['entity'];
    }

    // Check if Group module is available
    $group_module_exists = \Drupal::moduleHandler()->moduleExists('group');
    
    // Build target types array
    $target_types = ['user' => 'user'];
    if ($group_module_exists) {
      $target_types['group'] = 'group';
    }

    $form['assigned_entities']['entities'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Users and Groups'),
      '#target_type' => 'user', // Default, but will be overridden by tags
      '#tags' => TRUE,
      '#default_value' => $default_entities,
      '#description' => $this->t('Start typing to search for users' . ($group_module_exists ? ' or groups' : '') . '. You can select multiple items.'),
      '#element_validate' => [[$this, 'validateAssignedEntities']],
    ];

    // Add custom description for mixed selection
    if ($group_module_exists) {
      $form['assigned_entities']['help'] = [
        '#type' => 'item',
        '#markup' => $this->t('<em>Note: Type "user:" followed by a name to search for users, or "group:" followed by a name to search for groups.</em>'),
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
   * Validates the assigned entities field.
   */
  public function validateAssignedEntities($element, FormStateInterface $form_state) {
    // Custom validation if needed
    // The entity_autocomplete field handles most validation
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\dworkflow\WorkflowListInterface $workflow_list */
    $workflow_list = $this->entity;

    // Process assigned entities
    $entities_value = $form_state->getValue('entities');
    $assigned_entities = [];
    
    if (!empty($entities_value)) {
      // Parse the entity_autocomplete tags format
      foreach ($entities_value as $item) {
        if (isset($item['target_id'])) {
          // Determine entity type
          $entity = $this->entityTypeManager->getStorage('user')->load($item['target_id']);
          $entity_type = 'user';
          
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
