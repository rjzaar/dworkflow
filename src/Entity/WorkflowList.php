<?php

namespace Drupal\workflow_assignment\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

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
 *   links = {
 *     "collection" = "/admin/structure/workflow-list",
 *     "add-form" = "/admin/structure/workflow-list/add",
 *     "edit-form" = "/admin/structure/workflow-list/{workflow_list}/edit",
 *     "delete-form" = "/admin/structure/workflow-list/{workflow_list}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "assigned_users",
 *     "assigned_groups",
 *     "resource_tags",
 *     "destination_tags",
 *     "created",
 *     "changed"
 *   }
 * )
 */
class WorkflowList extends ConfigEntityBase {

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
   * Array of assigned user IDs.
   *
   * @var array
   */
  protected $assigned_users = [];

  /**
   * Array of assigned group IDs.
   *
   * @var array
   */
  protected $assigned_groups = [];

  /**
   * Array of resource location term IDs.
   *
   * @var array
   */
  protected $resource_tags = [];

  /**
   * Array of destination location term IDs.
   *
   * @var array
   */
  protected $destination_tags = [];

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
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    
    $this->changed = \Drupal::time()->getRequestTime();
    
    if ($this->isNew()) {
      $this->created = \Drupal::time()->getRequestTime();
    }
  }

  /**
   * Gets the description.
   *
   * @return string
   *   The description.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Sets the description.
   *
   * @param string $description
   *   The description.
   *
   * @return $this
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * Gets assigned users.
   *
   * @return array
   *   Array of user IDs.
   */
  public function getAssignedUsers() {
    return $this->assigned_users ?: [];
  }

  /**
   * Sets assigned users.
   *
   * @param array $users
   *   Array of user IDs.
   *
   * @return $this
   */
  public function setAssignedUsers(array $users) {
    $this->assigned_users = array_values(array_unique(array_filter($users)));
    return $this;
  }

  /**
   * Adds a user to the workflow.
   *
   * @param int $uid
   *   The user ID.
   *
   * @return $this
   */
  public function addAssignedUser($uid) {
    $users = $this->getAssignedUsers();
    if (!in_array($uid, $users)) {
      $users[] = $uid;
      $this->setAssignedUsers($users);
    }
    return $this;
  }

  /**
   * Removes a user from the workflow.
   *
   * @param int $uid
   *   The user ID.
   *
   * @return $this
   */
  public function removeAssignedUser($uid) {
    $users = $this->getAssignedUsers();
    $users = array_diff($users, [$uid]);
    $this->setAssignedUsers($users);
    return $this;
  }

  /**
   * Gets assigned groups.
   *
   * @return array
   *   Array of group IDs.
   */
  public function getAssignedGroups() {
    return $this->assigned_groups ?: [];
  }

  /**
   * Sets assigned groups.
   *
   * @param array $groups
   *   Array of group IDs.
   *
   * @return $this
   */
  public function setAssignedGroups(array $groups) {
    $this->assigned_groups = array_values(array_unique(array_filter($groups)));
    return $this;
  }

  /**
   * Adds a group to the workflow.
   *
   * @param int $gid
   *   The group ID.
   *
   * @return $this
   */
  public function addAssignedGroup($gid) {
    $groups = $this->getAssignedGroups();
    if (!in_array($gid, $groups)) {
      $groups[] = $gid;
      $this->setAssignedGroups($groups);
    }
    return $this;
  }

  /**
   * Removes a group from the workflow.
   *
   * @param int $gid
   *   The group ID.
   *
   * @return $this
   */
  public function removeAssignedGroup($gid) {
    $groups = $this->getAssignedGroups();
    $groups = array_diff($groups, [$gid]);
    $this->setAssignedGroups($groups);
    return $this;
  }

  /**
   * Gets resource tags.
   *
   * @return array
   *   Array of term IDs.
   */
  public function getResourceTags() {
    return $this->resource_tags ?: [];
  }

  /**
   * Sets resource tags.
   *
   * @param array $tags
   *   Array of term IDs.
   *
   * @return $this
   */
  public function setResourceTags(array $tags) {
    $this->resource_tags = array_values(array_unique(array_filter($tags)));
    return $this;
  }

  /**
   * Adds a resource tag.
   *
   * @param int $tid
   *   The term ID.
   *
   * @return $this
   */
  public function addResourceTag($tid) {
    $tags = $this->getResourceTags();
    if (!in_array($tid, $tags)) {
      $tags[] = $tid;
      $this->setResourceTags($tags);
    }
    return $this;
  }

  /**
   * Gets destination tags.
   *
   * @return array
   *   Array of destination term IDs.
   */
  public function getDestinationTags() {
    return $this->destination_tags ?: [];
  }

  /**
   * Sets destination tags.
   *
   * @param array $tags
   *   Array of destination term IDs.
   *
   * @return $this
   */
  public function setDestinationTags(array $tags) {
    $this->destination_tags = array_values(array_unique(array_filter($tags)));
    return $this;
  }

  /**
   * Adds a destination tag.
   *
   * @param int $tid
   *   The term ID.
   *
   * @return $this
   */
  public function addDestinationTag($tid) {
    $tags = $this->getDestinationTags();
    if (!in_array($tid, $tags)) {
      $tags[] = $tid;
      $this->setDestinationTags($tags);
    }
    return $this;
  }

  /**
   * Gets the creation timestamp.
   *
   * @return int
   *   The creation timestamp.
   */
  public function getCreatedTime() {
    return $this->created;
  }

  /**
   * Gets the last modified timestamp.
   *
   * @return int
   *   The last modified timestamp.
   */
  public function getChangedTime() {
    return $this->changed;
  }

}
