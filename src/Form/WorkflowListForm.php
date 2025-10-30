<?php

namespace Drupal\workflow_assignment\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the workflow list add and edit forms.
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

    /** @var \Drupal\workflow_assignment\WorkflowListInterface $workflow_list */
    $workflow_list = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $workflow_list->label(),
      '#description' => $this->t('Name for this workflow list.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $workflow_list->id(),
      '#machine_name' => [
        'exists' => '\Drupal\workflow_assignment\Entity\WorkflowList::load',
      ],
      '#disabled' => !$workflow_list->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $workflow_list->getDescription(),
      '#description' => $this->t('A description of this workflow list.'),
    ];

    // User assignment section
    $form['assignments'] = [
      '#type' => 'details',
      '#title' => $this->t('Assignments'),
      '#open' => TRUE,
    ];

    // Get all users for selection
    $user_storage = $this->entityTypeManager->getStorage('user');
    $user_ids = $user_storage->getQuery()
      ->condition('status', 1)
      ->condition('uid', 0, '>')
      ->sort('name')
      ->accessCheck(TRUE)
      ->execute();
    
    $users = $user_storage->loadMultiple($user_ids);
    $user_options = [];
    foreach ($users as $user) {
      $user_options[$user->id()] = $user->getDisplayName();
    }

    $form['assignments']['assigned_users'] = [
      '#type' => 'select',
      '#title' => $this->t('Assigned Users'),
      '#options' => $user_options,
      '#default_value' => $workflow_list->getAssignedUsers(),
      '#multiple' => TRUE,
      '#size' => 10,
      '#description' => $this->t('Select users to assign to this workflow list. You can change this at any time.'),
    ];

    // Check if group module exists
    $moduleHandler = \Drupal::service('module_handler');
    if ($moduleHandler->moduleExists('group')) {
      $group_storage = $this->entityTypeManager->getStorage('group');
      $group_ids = $group_storage->getQuery()
        ->sort('label')
        ->accessCheck(TRUE)
        ->execute();
      
      $groups = $group_storage->loadMultiple($group_ids);
      $group_options = [];
      foreach ($groups as $group) {
        $group_options[$group->id()] = $group->label();
      }

      $form['assignments']['assigned_groups'] = [
        '#type' => 'select',
        '#title' => $this->t('Assigned Groups'),
        '#options' => $group_options,
        '#default_value' => $workflow_list->getAssignedGroups(),
        '#multiple' => TRUE,
        '#size' => 10,
        '#description' => $this->t('Select groups to assign to this workflow list. You can change this at any time.'),
      ];
    }
    else {
      $form['assignments']['group_info'] = [
        '#markup' => '<p><em>' . $this->t('Group module is not installed. Enable it to assign groups to workflow lists.') . '</em></p>',
      ];
    }

    // Resource location section
    $form['resources'] = [
      '#type' => 'details',
      '#title' => $this->t('Resource Locations'),
      '#open' => TRUE,
    ];

    // Get resource location vocabulary
    $config = \Drupal::config('workflow_assignment.settings');
    $vocabulary_id = $config->get('resource_vocabulary') ?: 'resource_locations';

    // Get all terms from the vocabulary
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $terms = $term_storage->loadTree($vocabulary_id, 0, NULL, TRUE);
    
    $tag_options = [];
    foreach ($terms as $term) {
      $tag_options[$term->id()] = $term->getName();
    }

    if (empty($tag_options)) {
      $form['resources']['no_terms'] = [
        '#markup' => '<p><em>' . $this->t('No resource location tags available. Create tags in the @vocab vocabulary.', [
          '@vocab' => $vocabulary_id,
        ]) . '</em></p>',
      ];
    }
    else {
      $form['resources']['resource_tags'] = [
        '#type' => 'select',
        '#title' => $this->t('Resource Location Tags'),
        '#options' => $tag_options,
        '#default_value' => $workflow_list->getResourceTags(),
        '#multiple' => TRUE,
        '#size' => 10,
        '#description' => $this->t('Tag this workflow with resource locations. These designate where resources for this workflow can be found.'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\workflow_assignment\WorkflowListInterface $workflow_list */
    $workflow_list = $this->entity;
    
    // Set assigned users
    $assigned_users = $form_state->getValue('assigned_users');
    $workflow_list->setAssignedUsers(array_filter($assigned_users));
    
    // Set assigned groups if available
    if ($form_state->hasValue('assigned_groups')) {
      $assigned_groups = $form_state->getValue('assigned_groups');
      $workflow_list->setAssignedGroups(array_filter($assigned_groups));
    }
    
    // Set resource tags
    if ($form_state->hasValue('resource_tags')) {
      $resource_tags = $form_state->getValue('resource_tags');
      $workflow_list->setResourceTags(array_filter($resource_tags));
    }

    $status = $workflow_list->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created the %label workflow list.', [
        '%label' => $workflow_list->label(),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Updated the %label workflow list.', [
        '%label' => $workflow_list->label(),
      ]));
    }

    $form_state->setRedirectUrl($workflow_list->toUrl('collection'));
  }

}
