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
    
    // Get all assigned workflows
    $workflow_ids = [];
    if ($node->hasField('field_workflow_list')) {
      foreach ($node->get('field_workflow_list') as $item) {
        if (!empty($item->value)) {
          $workflow_ids[] = $item->value;
        }
      }
    }

    $can_edit = $this->currentUser()->hasPermission('assign workflow lists to content');

    if (!empty($workflow_ids)) {
      // Load all workflows
      $workflows = $this->entityTypeManager
        ->getStorage('workflow_list')
        ->loadMultiple($workflow_ids);

      if (!empty($workflows)) {
        // Build table of assignments
        $build['workflow_table'] = [
          '#type' => 'table',
          '#header' => [
            $this->t('Workflow Name'),
            $this->t('Description'),
            $this->t('Assigned Users'),
            $this->t('Assigned Groups'),
            $this->t('Resource Locations'),
            $this->t('Destination Locations'),
          ],
          '#rows' => [],
          '#attributes' => [
            'class' => ['workflow-assignments-table'],
          ],
          '#attached' => [
            'library' => ['workflow_assignment/workflow_tab'],
          ],
        ];

        foreach ($workflows as $workflow) {
          $build['workflow_table']['#rows'][] = $this->buildWorkflowRow($workflow);
        }
      }
    }
    else {
      $build['no_workflow'] = [
        '#markup' => '<div class="no-workflow-message"><p>' . $this->t('No workflows are currently assigned to this content.') . '</p></div>',
      ];
    }

    if ($can_edit) {
      $build['assign_form'] = [
        '#type' => 'link',
        '#title' => !empty($workflow_ids) ? $this->t('Manage Workflows') : $this->t('Assign Workflows'),
        '#url' => Url::fromRoute('workflow_assignment.node_assign', ['node' => $node->id()]),
        '#attributes' => [
          'class' => ['button', 'button--primary'],
        ],
      ];
    }

    $build['#attached']['library'][] = 'workflow_assignment/workflow_tab';

    return $build;
  }

  /**
   * Builds a table row for a workflow.
   *
   * @param \Drupal\workflow_assignment\Entity\WorkflowList $workflow
   *   The workflow entity.
   *
   * @return array
   *   Table row array.
   */
  protected function buildWorkflowRow($workflow) {
    $row = [];
    
    // Workflow name
    $row[] = [
      'data' => [
        '#markup' => '<strong>' . $workflow->label() . '</strong>',
      ],
    ];

    // Description
    $description = $workflow->getDescription();
    $row[] = [
      'data' => [
        '#markup' => $description ? $description : '-',
      ],
    ];

    // Assigned users
    $users = $workflow->getAssignedUsers();
    if (!empty($users)) {
      $user_storage = $this->entityTypeManager->getStorage('user');
      $user_names = [];
      foreach ($users as $uid) {
        $user = $user_storage->load($uid);
        if ($user) {
          $user_names[] = $user->getDisplayName();
        }
      }
      $row[] = [
        'data' => [
          '#markup' => implode(', ', $user_names),
        ],
      ];
    }
    else {
      $row[] = '-';
    }

    // Assigned groups
    $groups = $workflow->getAssignedGroups();
    if (!empty($groups)) {
      $group_storage = $this->entityTypeManager->getStorage('group');
      $group_names = [];
      foreach ($groups as $gid) {
        $group = $group_storage->load($gid);
        if ($group) {
          $group_names[] = $group->label();
        }
      }
      $row[] = [
        'data' => [
          '#markup' => implode(', ', $group_names),
        ],
      ];
    }
    else {
      $row[] = '-';
    }

    // Resource locations
    $resources = $workflow->getResourceTags();
    if (!empty($resources)) {
      $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
      $resource_names = [];
      foreach ($resources as $tid) {
        $term = $term_storage->load($tid);
        if ($term) {
          $resource_names[] = $term->getName();
        }
      }
      $row[] = [
        'data' => [
          '#markup' => implode(', ', $resource_names),
        ],
      ];
    }
    else {
      $row[] = '-';
    }

    // Destination locations (with color coding)
    $destinations = $workflow->getDestinationTags();
    if (!empty($destinations)) {
      $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
      $destination_items = [];
      foreach ($destinations as $tid) {
        $term = $term_storage->load($tid);
        if ($term) {
          $term_name = $term->getName();
          $class = 'destination-badge--' . strtolower($term_name);
          $destination_items[] = '<span class="destination-badge ' . $class . '">📍 ' . $term_name . '</span>';
        }
      }
      $row[] = [
        'data' => [
          '#markup' => implode(' ', $destination_items),
        ],
      ];
    }
    else {
      $row[] = '-';
    }

    return $row;
  }

}
