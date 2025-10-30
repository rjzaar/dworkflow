<?php

namespace Drupal\dworkflow\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\dworkflow\WorkflowListInterface;

/**
 * Defines the Workflow List entity.
 *
 * @ConfigEntityType(
 *   id = "workflow_list",
 *   label = @Translation("Workflow List"),
 *   label_collection = @Translation("Workflow Lists"),
 *   label_singular = @Translation("workflow list"),
 *   label_plural = @Translation("workflow lists"),
 *   label_count = @PluralTranslation(
 *     singular = "@count workflow list",
 *     plural = "@count workflow lists",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\dworkflow\WorkflowListListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dworkflow\Form\WorkflowListForm",
 *       "edit" = "Drupal\dworkflow\Form\WorkflowListForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "workflow_list",
 *   admin_permission = "administer workflow lists",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/workflow-list/{workflow_list}",
 *     "add-form" = "/admin/structure/workflow-list/add",
 *     "edit-form" = "/admin/structure/workflow-list/{workflow_list}/edit",
 *     "delete-form" = "/admin/structure/workflow-list/{workflow_list}/delete",
 *     "collection" = "/admin/structure/workflow-list"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "assigned_entities",
 *     "resource_tags",
 *     "created",
 *     "changed"
 *   }
 * )
 */
class WorkflowList extends ConfigEntityBase implements WorkflowListInterface {

  /**
   * The Workflow List ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Workflow List label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Workflow List description.
   *
   * @var string
   */
  protected $description;

  /**
   * Assigned entities (users and/or groups).
   *
   * Format: [
   *   ['target_type' => 'user', 'target_id' => 5],
   *   ['target_type' => 'group', 'target_id' => 2],
   * ]
   *
   * @var array
   */
  protected $assigned_entities = [];

  /**
   * Resource location tags (taxonomy term IDs).
   *
   * @var array
   */
  protected $resource_tags = [];

  /**
   * The timestamp when the workflow was created.
   *
   * @var int
   */
  protected $created;

  /**
   * The timestamp when the workflow was last changed.
   *
   * @var int
   */
  protected $changed;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAssignedEntities() {
    $entities = [];
    
    if (empty($this->assigned_entities)) {
      return $entities;
    }

    foreach ($this->assigned_entities as $item) {
      if (isset($item['target_id']) && isset($item['target_type'])) {
        try {
          $entity = \Drupal::entityTypeManager()
            ->getStorage($item['target_type'])
            ->load($item['target_id']);
          
          if ($entity) {
            $entities[] = [
              'entity_type' => $item['target_type'],
              'entity_id' => $item['target_id'],
              'entity' => $entity,
            ];
          }
        }
        catch (\Exception $e) {
          // Log error but continue processing other entities
          \Drupal::logger('dworkflow')->error('Error loading entity @type:@id: @message', [
            '@type' => $item['target_type'],
            '@id' => $item['target_id'],
            '@message' => $e->getMessage(),
          ]);
        }
      }
    }
    
    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function getAssignedUsers() {
    $users = [];
    foreach ($this->getAssignedEntities() as $item) {
      if ($item['entity_type'] === 'user') {
        $users[] = $item['entity_id'];
      }
    }
    return $users;
  }

  /**
   * {@inheritdoc}
   */
  public function getAssignedGroups() {
    $groups = [];
    foreach ($this->getAssignedEntities() as $item) {
      if ($item['entity_type'] === 'group') {
        $groups[] = $item['entity_id'];
      }
    }
    return $groups;
  }

  /**
   * {@inheritdoc}
   */
  public function setAssignedEntities(array $entities) {
    $this->assigned_entities = $entities;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addAssignedEntity($entity_type, $entity_id) {
    // Initialize if null
    if (!is_array($this->assigned_entities)) {
      $this->assigned_entities = [];
    }

    // Check if already assigned
    foreach ($this->assigned_entities as $item) {
      if ($item['target_type'] === $entity_type && $item['target_id'] == $entity_id) {
        return $this;
      }
    }
    
    // Add new assignment
    $this->assigned_entities[] = [
      'target_type' => $entity_type,
      'target_id' => $entity_id,
    ];
    
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAssignedEntity($entity_type, $entity_id) {
    if (!is_array($this->assigned_entities)) {
      return $this;
    }

    $new_assigned = [];
    
    foreach ($this->assigned_entities as $item) {
      if (!($item['target_type'] === $entity_type && $item['target_id'] == $entity_id)) {
        $new_assigned[] = $item;
      }
    }
    
    $this->assigned_entities = $new_assigned;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addAssignedUser($user_id) {
    return $this->addAssignedEntity('user', $user_id);
  }

  /**
   * {@inheritdoc}
   */
  public function addAssignedGroup($group_id) {
    return $this->addAssignedEntity('group', $group_id);
  }

  /**
   * {@inheritdoc}
   */
  public function removeAssignedUser($user_id) {
    return $this->removeAssignedEntity('user', $user_id);
  }

  /**
   * {@inheritdoc}
   */
  public function removeAssignedGroup($group_id) {
    return $this->removeAssignedEntity('group', $group_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceTags() {
    return $this->resource_tags ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function setResourceTags(array $tags) {
    $this->resource_tags = $tags;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addResourceTag($tag_id) {
    if (!is_array($this->resource_tags)) {
      $this->resource_tags = [];
    }

    if (!in_array($tag_id, $this->resource_tags)) {
      $this->resource_tags[] = $tag_id;
    }
    
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeResourceTag($tag_id) {
    if (is_array($this->resource_tags)) {
      $this->resource_tags = array_values(array_diff($this->resource_tags, [$tag_id]));
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->created;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->created = $timestamp;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->changed;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->changed = $timestamp;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Set created time for new entities
    if ($this->isNew()) {
      $this->created = \Drupal::time()->getRequestTime();
    }

    // Always update changed time
    $this->changed = \Drupal::time()->getRequestTime();
  }

}
