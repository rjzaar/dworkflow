<?php

namespace Drupal\workflow_assignment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\workflow_assignment\Entity\WorkflowList;

/**
 * Quick edit form for workflow lists.
 */
class QuickEditWorkflowForm extends FormBase {

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
   * Constructs a QuickEditWorkflowForm object.
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
  public function getFormId() {
    return 'workflow_assignment_quick_edit';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, WorkflowList $workflow_list = NULL) {
    if (!$workflow_list) {
      return $form;
    }

    $form_state->set('workflow', $workflow_list);

    $form['info'] = [
      '#markup' => '<h2>' . $this->t('Quick Edit: @name', ['@name' => $workflow_list->label()]) . '</h2>',
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
      '#default_value' => $workflow_list->getAssignedUsers(),
      '#multiple' => TRUE,
      '#size' => 10,
    ];

    // Groups (if available).
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
        '#default_value' => $workflow_list->getAssignedGroups(),
        '#multiple' => TRUE,
        '#size' => 10,
      ];
    }

    // Resource tags.
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
      '#title' => $this->t('Resource Locations'),
      '#options' => $resource_options,
      '#default_value' => $workflow_list->getResourceTags(),
      '#multiple' => TRUE,
      '#size' => 6,
    ];

    // Destination tags.
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
      '#default_value' => $workflow_list->getDestinationTags(),
      '#multiple' => TRUE,
      '#size' => 6,
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
    /** @var \Drupal\workflow_assignment\Entity\WorkflowList $workflow */
    $workflow = $form_state->get('workflow');

    $workflow->setAssignedUsers($form_state->getValue('assigned_users'));
    if ($this->moduleHandler->moduleExists('group')) {
      $workflow->setAssignedGroups($form_state->getValue('assigned_groups'));
    }
    $workflow->setResourceTags($form_state->getValue('resource_tags'));
    $workflow->setDestinationTags($form_state->getValue('destination_tags'));
    
    $workflow->save();

    $this->messenger()->addStatus($this->t('Workflow %label has been updated.', [
      '%label' => $workflow->label(),
    ]));

    $form_state->setRedirectUrl($workflow->toUrl('collection'));
  }

}
