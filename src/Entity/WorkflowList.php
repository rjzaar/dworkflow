<?php

namespace Drupal\workflow_assignment\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\workflow_assignment\WorkflowListInterface;

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
 *     "list_builder" = "Drupal\workflow_assignment\WorkflowListListBuilder",
 *     "form" = {
 *       "add" = "Drupal\workflow_assignment\Form\WorkflowListForm",
 *       "edit" = "Drupal\workflow_assignment\Form\WorkflowListForm",
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
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "assigned_users",
 *     "assigned_groups",
 *     "resource_tags",
 *     "created",
 *     "changed"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/workflow-list/add",
 *     "edit-form" = "/admin/structure/workflow-list/{workflow_list}/edit",
 *     "delete-form" = "/admin/structure/workflow-list/{workflow_list}/delete",
 *     "collection" = "/admin/structure/workflow-list"
 *   }
 * )
 */
class WorkflowList extends ConfigEntityBase implements WorkflowListInterface {

  /**
   * The workflow list ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The workflow list label.
   *
   * @var string
   */
  protected $label;

  /**
   * The workflow list description.
   *
   * @var string
   */
  protected $description;

  /**
   * The assigned user IDs.
   *
   * @var array
   */
  protected $assigned_users = [];

  /**
   * The assigned group IDs.
   *
   * @var array
   */
  protected $assigned_groups = [];

  /**
   * The resource location tag term IDs.
   *
   * @var array
   */
  protected $resource_tags = [];

  /**
   * The creation timestamp.
   *
   * @var int
   */
  protected $created;

  /**
   * The last modified timestamp.
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
  public function getAssignedUsers() {
    return $this->assigned_users ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function setAssignedUsers(array $users) {
    $this->assigned_users = $users;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addAssignedUser($user_id) {
    if (!in_array($user_id, $this->assigned_users)) {
      $this->assigned_users[] = $user_id;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAssignedUser($user_id) {
    $this->assigned_users = array_diff($this->assigned_users, [$user_id]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAssignedGroups() {
    return $this->assigned_groups ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function setAssignedGroups(array $groups) {
    $this->assigned_groups = $groups;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addAssignedGroup($group_id) {
    if (!in_array($group_id, $this->assigned_groups)) {
      $this->assigned_groups[] = $group_id;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAssignedGroup($group_id) {
    $this->assigned_groups = array_diff($this->assigned_groups, [$group_id]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceTags() {
    return $this->resource_tags ?: [];
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
  public function addResourceTag($term_id) {
    if (!in_array($term_id, $this->resource_tags)) {
      $this->resource_tags[] = $term_id;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeResourceTag($term_id) {
    $this->resource_tags = array_diff($this->resource_tags, [$term_id]);
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
  public function preSave(\Drupal\Core\Entity\EntityStorageInterface $storage) {
    parent::preSave($storage);

    if ($this->isNew()) {
      $this->created = \Drupal::time()->getRequestTime();
    }
    $this->changed = \Drupal::time()->getRequestTime();
  }

}
