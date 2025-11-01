<?php

namespace Drupal\workflow_assignment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Controller for the workflow tab on nodes.
 */
class NodeWorkflowController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a NodeWorkflowController object.
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
   * Displays the workflow tab content.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @return array
   *   A render array.
   */
  public function workflowTab(NodeInterface $node) {
    $build = [];
    
    // Check if node has a workflow assigned.
    $workflow_id = NULL;
    if ($node->hasField('field_workflow_list')) {
      $workflow_id = $node->get('field_workflow_list')->value;
    }

    $workflow = NULL;
    if ($workflow_id) {
      $workflow = $this->entityTypeManager
        ->getStorage('workflow_list')
        ->load($workflow_id);
    }

    $can_edit = $this->currentUser()->hasPermission('assign workflow lists to content');

    if ($workflow) {
      $build['workflow_info'] = [
        '#theme' => 'workflow_tab_content',
        '#workflow' => $workflow,
        '#node' => $node,
        '#can_edit' => $can_edit,
      ];
    }
    else {
      $build['no_workflow'] = [
        '#markup' => '<p>' . $this->t('No workflow is currently assigned to this content.') . '</p>',
      ];
    }

    if ($can_edit) {
      $build['assign_form'] = [
        '#type' => 'link',
        '#title' => $workflow ? $this->t('Change Workflow') : $this->t('Assign Workflow'),
        '#url' => Url::fromRoute('workflow_assignment.node_assign', ['node' => $node->id()]),
        '#attributes' => [
          'class' => ['button', 'button--primary'],
        ],
      ];
    }

    $build['#attached']['library'][] = 'workflow_assignment/workflow_tab';

    return $build;
  }

}
