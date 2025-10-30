<?php

namespace Drupal\dworkflow\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to assign workflow lists to nodes.
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
    return 'dworkflow_node_assign_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    if (!$node) {
      $this->messenger()->addError($this->t('Node not found.'));
      return $form;
    }

    $form_state->set('node', $node);

    // Check if this content type is enabled
    $config = \Drupal::config('dworkflow.settings');
    $enabled_types = $config->get('enabled_content_types') ?: [];
    
    if (!in_array($node->bundle(), $enabled_types)) {
      $form['warning'] = [
        '#markup' => '<p>' . $this->t('This content type is not enabled for workflow assignments. Please enable it in the <a href="@url">DWorkflow settings</a>.', [
          '@url' => '/admin/config/workflow/dworkflow',
        ]) . '</p>',
      ];
      return $form;
    }

    $form['info'] = [
      '#type' => 'item',
      '#markup' => $this->t('<h3>Assign Workflow to: @title</h3>', [
        '@title' => $node->getTitle(),
      ]),
    ];

    // Get current workflow assignment
    $current_workflow = NULL;
    if ($node->hasField('field_workflow_list') && !$node->get('field_workflow_list')->isEmpty()) {
      $current_workflow = $node->get('field_workflow_list')->target_id;
    }

    // Load all workflow lists
    $workflow_storage = $this->entityTypeManager->getStorage('workflow_list');
    $workflows = $workflow_storage->loadMultiple();
    
    $options = ['' => $this->t('- None -')];
    foreach ($workflows as $id => $workflow) {
      $options[$id] = $workflow->label();
    }

    $form['workflow_list'] = [
      '#type' => 'select',
      '#title' => $this->t('Workflow List'),
      '#options' => $options,
      '#default_value' => $current_workflow,
      '#description' => $this->t('Select a workflow list to assign to this content, or select "None" to remove the assignment.'),
    ];

    // Show current workflow info if assigned
    if ($current_workflow && isset($workflows[$current_workflow])) {
      $workflow = $workflows[$current_workflow];
      $info_parts = [];
      
      $entities = $workflow->getAssignedEntities();
      if (!empty($entities)) {
        $entity_names = [];
        foreach ($entities as $item) {
          $entity_names[] = $item['entity']->label() . ' (' . $item['entity_type'] . ')';
        }
        $info_parts[] = '<strong>' . $this->t('Assigned:') . '</strong> ' . implode(', ', $entity_names);
      }
      
      $tags = $workflow->getResourceTags();
      if (!empty($tags)) {
        $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadMultiple($tags);
        $tag_names = [];
        foreach ($terms as $term) {
          $tag_names[] = $term->label();
        }
        $info_parts[] = '<strong>' . $this->t('Resources:') . '</strong> ' . implode(', ', $tag_names);
      }
      
      if (!empty($info_parts)) {
        $form['current_info'] = [
          '#type' => 'item',
          '#title' => $this->t('Current Workflow Information'),
          '#markup' => '<div class="workflow-info">' . implode('<br>', $info_parts) . '</div>',
        ];
      }
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Assign Workflow'),
      '#button_type' => 'primary',
    ];

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => $node->toUrl(),
      '#attributes' => ['class' => ['button']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $form_state->get('node');
    $workflow_id = $form_state->getValue('workflow_list');

    // Ensure the field exists
    if (!$node->hasField('field_workflow_list')) {
      $this->messenger()->addError($this->t('Workflow field not found on this content type.'));
      return;
    }

    // Assign or remove workflow
    if (!empty($workflow_id)) {
      $node->set('field_workflow_list', $workflow_id);
      $workflow = $this->entityTypeManager->getStorage('workflow_list')->load($workflow_id);
      $this->messenger()->addMessage($this->t('Workflow "%workflow" has been assigned to this content.', [
        '%workflow' => $workflow ? $workflow->label() : $workflow_id,
      ]));
    }
    else {
      $node->set('field_workflow_list', NULL);
      $this->messenger()->addMessage($this->t('Workflow assignment has been removed from this content.'));
    }

    $node->save();

    $form_state->setRedirectUrl($node->toUrl());
  }

}
