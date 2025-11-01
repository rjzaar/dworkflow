<?php

namespace Drupal\workflow_assignment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Form for assigning workflows to nodes.
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
    return 'workflow_assignment_node_assign';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    if (!$node) {
      return $form;
    }

    $form_state->set('node', $node);

    $form['info'] = [
      '#markup' => '<p>' . $this->t('Assign or change the workflow for: <strong>@title</strong>', [
        '@title' => $node->getTitle(),
      ]) . '</p>',
    ];

    // Get all workflow lists.
    $workflow_storage = $this->entityTypeManager->getStorage('workflow_list');
    $workflows = $workflow_storage->loadMultiple();
    
    $workflow_options = ['' => $this->t('- None -')];
    foreach ($workflows as $workflow) {
      $workflow_options[$workflow->id()] = $workflow->label();
    }

    // Get current workflow.
    $current_workflow = NULL;
    if ($node->hasField('field_workflow_list')) {
      $current_workflow = $node->get('field_workflow_list')->value;
    }

    $form['workflow_list'] = [
      '#type' => 'select',
      '#title' => $this->t('Workflow List'),
      '#options' => $workflow_options,
      '#default_value' => $current_workflow,
      '#required' => FALSE,
      '#description' => $this->t('Select a workflow list to assign to this content, or select "None" to remove the current workflow.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
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

    if ($node->hasField('field_workflow_list')) {
      $node->set('field_workflow_list', $workflow_id ?: NULL);
      $node->save();

      if ($workflow_id) {
        $workflow = $this->entityTypeManager
          ->getStorage('workflow_list')
          ->load($workflow_id);
        
        $this->messenger()->addStatus($this->t('Workflow "@workflow" has been assigned to this content.', [
          '@workflow' => $workflow->label(),
        ]));
      }
      else {
        $this->messenger()->addStatus($this->t('Workflow has been removed from this content.'));
      }
    }

    // Redirect to the workflow tab.
    $form_state->setRedirect('workflow_assignment.node_workflow_tab', [
      'node' => $node->id(),
    ]);
  }

}
