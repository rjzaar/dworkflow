<?php

namespace Drupal\workflow_assignment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Quick edit form for modifying workflow lists on the fly.
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
    return 'workflow_assignment_quick_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $workflow_list = NULL) {
    if (!$workflow_list) {
      $form['message'] = [
        '#markup' => $this->t('No workflow list specified.'),
      ];
      return $form;
    }

    // Load the workflow list entity
    $workflow_entity = $this->entityTypeManager
      ->getStorage('workflow_list')
      ->load($workflow_list);

    if (!$workflow_entity) {
      $form['message'] = [
        '#markup' => $this->t('Invalid workflow list.'),
      ];
      return $form;
    }

    $form_state->set('workflow_list_id', $workflow_list);

    $form['info'] = [
      '#markup' => '<h2>' . $this->t('Quick Edit: @name', ['@name' => $workflow_entity->label()]) . '</h2>',
    ];

    // Quick user management
    $form['users'] = [
      '#type' => 'details',
      '#title' => $this->t('Modify Assigned Users'),
      '#open' => TRUE,
    ];

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

    $form['users']['assigned_users'] = [
      '#type' => 'select',
      '#title' => $this->t('Assigned Users'),
      '#options' => $user_options,
      '#default_value' => $workflow_entity->getAssignedUsers(),
      '#multiple' => TRUE,
      '#size' => 8,
    ];

    // Quick group management
    $moduleHandler = \Drupal::service('module_handler');
    if ($moduleHandler->moduleExists('group')) {
      $form['groups'] = [
        '#type' => 'details',
        '#title' => $this->t('Modify Assigned Groups'),
        '#open' => TRUE,
      ];

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

      $form['groups']['assigned_groups'] = [
        '#type' => 'select',
        '#title' => $this->t('Assigned Groups'),
        '#options' => $group_options,
        '#default_value' => $workflow_entity->getAssignedGroups(),
        '#multiple' => TRUE,
        '#size' => 8,
      ];
    }

    // Quick resource tag management
    $form['resources'] = [
      '#type' => 'details',
      '#title' => $this->t('Modify Resource Locations'),
      '#open' => TRUE,
    ];

    $config = \Drupal::config('workflow_assignment.settings');
    $vocabulary_id = $config->get('resource_vocabulary') ?: 'resource_locations';

    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $terms = $term_storage->loadTree($vocabulary_id, 0, NULL, TRUE);
    
    $tag_options = [];
    foreach ($terms as $term) {
      $tag_options[$term->id()] = $term->getName();
    }

    $form['resources']['resource_tags'] = [
      '#type' => 'select',
      '#title' => $this->t('Resource Location Tags'),
      '#options' => $tag_options,
      '#default_value' => $workflow_entity->getResourceTags(),
      '#multiple' => TRUE,
      '#size' => 8,
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
      '#url' => \Drupal\Core\Url::fromRoute('entity.workflow_list.collection'),
      '#attributes' => ['class' => ['button']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $workflow_list_id = $form_state->get('workflow_list_id');
    
    $workflow_entity = $this->entityTypeManager
      ->getStorage('workflow_list')
      ->load($workflow_list_id);

    if ($workflow_entity) {
      // Update users
      $assigned_users = $form_state->getValue('assigned_users');
      $workflow_entity->setAssignedUsers(array_filter($assigned_users));

      // Update groups if available
      if ($form_state->hasValue('assigned_groups')) {
        $assigned_groups = $form_state->getValue('assigned_groups');
        $workflow_entity->setAssignedGroups(array_filter($assigned_groups));
      }

      // Update resource tags
      if ($form_state->hasValue('resource_tags')) {
        $resource_tags = $form_state->getValue('resource_tags');
        $workflow_entity->setResourceTags(array_filter($resource_tags));
      }

      $workflow_entity->save();

      $this->messenger()->addStatus($this->t('Workflow list has been updated on the fly.'));
    }

    $form_state->setRedirect('entity.workflow_list.collection');
  }

}
