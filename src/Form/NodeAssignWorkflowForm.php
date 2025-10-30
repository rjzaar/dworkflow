<?php

namespace Drupal\workflow_assignment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for assigning workflow lists to nodes.
 */
class NodeAssignWorkflowForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a NodeAssignWorkflowForm object.
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
    return 'workflow_assignment_node_assign_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    if (!$node) {
      $form['message'] = [
        '#markup' => $this->t('No content specified.'),
      ];
      return $form;
    }

    // Store node ID
    $form_state->set('node_id', $node);

    // Load the node
    $node_entity = $this->entityTypeManager->getStorage('node')->load($node);
    
    if (!$node_entity) {
      $form['message'] = [
        '#markup' => $this->t('Invalid content.'),
      ];
      return $form;
    }

    // Get current workflow assignment
    $current_workflow = NULL;
    if ($node_entity->hasField('field_workflow_list') && !$node_entity->get('field_workflow_list')->isEmpty()) {
      $current_workflow = $node_entity->get('field_workflow_list')->value;
    }

    $form['content_info'] = [
      '#type' => 'item',
      '#markup' => '<h3>' . $this->t('Content: @title', ['@title' => $node_entity->getTitle()]) . '</h3>',
    ];

    // Display current workflow if assigned
    if ($current_workflow) {
      $workflow_list = $this->entityTypeManager
        ->getStorage('workflow_list')
        ->load($current_workflow);
      
      if ($workflow_list) {
        $form['current_workflow'] = [
          '#type' => 'details',
          '#title' => $this->t('Current Workflow'),
          '#open' => TRUE,
        ];

        $form['current_workflow']['info'] = [
          '#markup' => '<p><strong>' . $workflow_list->label() . '</strong></p>',
        ];

        // Show assigned users
        $users = $workflow_list->getAssignedUsers();
        if (!empty($users)) {
          $user_entities = $this->entityTypeManager->getStorage('user')->loadMultiple($users);
          $user_names = array_map(function($user) {
            return $user->getDisplayName();
          }, $user_entities);
          
          $form['current_workflow']['users'] = [
            '#markup' => '<p><strong>' . $this->t('Assigned Users:') . '</strong> ' . 
                         implode(', ', $user_names) . '</p>',
          ];
        }

        // Show resource locations
        $tags = $workflow_list->getResourceTags();
        if (!empty($tags)) {
          $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadMultiple($tags);
          $tag_names = array_map(function($term) {
            return $term->getName();
          }, $terms);
          
          $form['current_workflow']['resources'] = [
            '#markup' => '<p><strong>' . $this->t('Resource Locations:') . '</strong> ' . 
                         implode(', ', $tag_names) . '</p>',
          ];
        }
      }
    }

    // Get all workflow lists
    $workflow_lists = $this->entityTypeManager
      ->getStorage('workflow_list')
      ->loadMultiple();

    $workflow_options = ['' => $this->t('- None -')];
    foreach ($workflow_lists as $workflow_list) {
      $workflow_options[$workflow_list->id()] = $workflow_list->label();
    }

    $form['workflow_list'] = [
      '#type' => 'select',
      '#title' => $this->t('Assign Workflow List'),
      '#options' => $workflow_options,
      '#default_value' => $current_workflow,
      '#description' => $this->t('Select a workflow list to assign to this content. This can be changed at any time.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Assign Workflow'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node_id = $form_state->get('node_id');
    $workflow_list_id = $form_state->getValue('workflow_list');

    $node = $this->entityTypeManager->getStorage('node')->load($node_id);
    
    if ($node && $node->hasField('field_workflow_list')) {
      $node->set('field_workflow_list', $workflow_list_id);
      $node->save();

      if (empty($workflow_list_id)) {
        $this->messenger()->addStatus($this->t('Workflow list has been removed from this content.'));
      }
      else {
        $workflow_list = $this->entityTypeManager
          ->getStorage('workflow_list')
          ->load($workflow_list_id);
        $this->messenger()->addStatus($this->t('Workflow list "@workflow" has been assigned to this content.', [
          '@workflow' => $workflow_list->label(),
        ]));
      }
    }
    else {
      $this->messenger()->addError($this->t('Failed to assign workflow list.'));
    }

    $form_state->setRedirect('entity.node.canonical', ['node' => $node_id]);
  }

}
