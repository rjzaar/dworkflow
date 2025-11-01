<?php

namespace Drupal\workflow_assignment\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

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
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a WorkflowListForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\workflow_assignment\Entity\WorkflowList $workflow */
    $workflow = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $workflow->label(),
      '#description' => $this->t('Name for this workflow list.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $workflow->id(),
      '#machine_name' => [
        'exists' => '\Drupal\workflow_assignment\Entity\WorkflowList::load',
      ],
      '#disabled' => !$workflow->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $workflow->getDescription(),
      '#description' => $this->t('Optional description of this workflow.'),
    ];

    // Users selection.
    $user_storage = $this->entityTypeManager->getStorage('user');
    $user_options = [];
    $users = $user_storage->loadMultiple();
    foreach ($users as $uid => $user) {
      if ($uid > 0) {
        $user_options[$uid] = $user->getDisplayName();
      }
    }

    $form['assigned_users'] = [
      '#type' => 'select',
      '#title' => $this->t('Assigned Users'),
      '#options' => $user_options,
      '#default_value' => $workflow->getAssignedUsers(),
      '#multiple' => TRUE,
      '#size' => 10,
      '#description' => $this->t('Select users to assign to this workflow.'),
    ];

    // Groups selection (if Group module is available).
    if ($this->moduleHandler->moduleExists('group')) {
      $group_storage = $this->entityTypeManager->getStorage('group');
      $group_options = [];
      $groups = $group_storage->loadMultiple();
      foreach ($groups as $gid => $group) {
        $group_options[$gid] = $group->label();
      }

      $form['assigned_groups'] = [
        '#type' => 'select',
        '#title' => $this->t('Assigned Groups'),
        '#options' => $group_options,
        '#default_value' => $workflow->getAssignedGroups(),
        '#multiple' => TRUE,
        '#size' => 10,
        '#description' => $this->t('Select groups to assign to this workflow.'),
      ];
    }

    // Resource location tags.
    $config = $this->config('workflow_assignment.settings');
    $resource_vocab = $config->get('resource_vocabulary') ?: 'resource_locations';
    
    $resource_terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadTree($resource_vocab);
    
    $resource_options = [];
    foreach ($resource_terms as $term) {
      $resource_options[$term->tid] = $term->name;
    }

    $form['resource_tags'] = [
      '#type' => 'select',
      '#title' => $this->t('Resource Location Tags'),
      '#options' => $resource_options,
      '#default_value' => $workflow->getResourceTags(),
      '#multiple' => TRUE,
      '#size' => 8,
      '#description' => $this->t('Tag this workflow with resource locations.'),
    ];

    // Destination location tags - NEW FEATURE.
    $destination_vocab = $config->get('destination_vocabulary') ?: 'destination_locations';
    
    $destination_terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadTree($destination_vocab);
    
    $destination_options = [];
    foreach ($destination_terms as $term) {
      $destination_options[$term->tid] = $term->name;
    }

    $form['destination_tags'] = [
      '#type' => 'select',
      '#title' => $this->t('Destination Locations'),
      '#options' => $destination_options,
      '#default_value' => $workflow->getDestinationTags(),
      '#multiple' => TRUE,
      '#size' => 8,
      '#description' => $this->t('Select destination locations for this workflow (e.g., Public, Private).'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $workflow = $this->entity;
    
    // Set values from form.
    $workflow->setAssignedUsers($form_state->getValue('assigned_users'));
    if ($this->moduleHandler->moduleExists('group')) {
      $workflow->setAssignedGroups($form_state->getValue('assigned_groups'));
    }
    $workflow->setResourceTags($form_state->getValue('resource_tags'));
    $workflow->setDestinationTags($form_state->getValue('destination_tags'));
    
    $status = $workflow->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created workflow list %label.', [
        '%label' => $workflow->label(),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Updated workflow list %label.', [
        '%label' => $workflow->label(),
      ]));
    }

    $form_state->setRedirectUrl($workflow->toUrl('collection'));
  }

}
