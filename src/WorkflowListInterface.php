<?php

namespace Drupal\workflow_assignment;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a workflow list entity.
 */
interface WorkflowListInterface extends ConfigEntityInterface {

  /**
   * Gets the description.
   *
   * @return string
   *   The description.
   */
  public function getDescription();

  /**
   * Sets the description.
   *
   * @param string $description
   *   The description.
   *
   * @return $this
   */
  public function setDescription($description);

  /**
   * Gets the assigned user IDs.
   *
   * @return array
   *   Array of user IDs.
   */
  public function getAssignedUsers();

  /**
   * Sets the assigned user IDs.
   *
   * @param array $users
   *   Array of user IDs.
   *
   * @return $this
   */
  public function setAssignedUsers(array $users);

  /**
   * Adds a user to the assignment list.
   *
   * @param int $user_id
   *   The user ID to add.
   *
   * @return $this
   */
  public function addAssignedUser($user_id);

  /**
   * Removes a user from the assignment list.
   *
   * @param int $user_id
   *   The user ID to remove.
   *
   * @return $this
   */
  public function removeAssignedUser($user_id);

  /**
   * Gets the assigned group IDs.
   *
   * @return array
   *   Array of group IDs.
   */
  public function getAssignedGroups();

  /**
   * Sets the assigned group IDs.
   *
   * @param array $groups
   *   Array of group IDs.
   *
   * @return $this
   */
  public function setAssignedGroups(array $groups);

  /**
   * Adds a group to the assignment list.
   *
   * @param int $group_id
   *   The group ID to add.
   *
   * @return $this
   */
  public function addAssignedGroup($group_id);

  /**
   * Removes a group from the assignment list.
   *
   * @param int $group_id
   *   The group ID to remove.
   *
   * @return $this
   */
  public function removeAssignedGroup($group_id);

  /**
   * Gets the resource location tag term IDs.
   *
   * @return array
   *   Array of taxonomy term IDs.
   */
  public function getResourceTags();

  /**
   * Sets the resource location tag term IDs.
   *
   * @param array $tags
   *   Array of taxonomy term IDs.
   *
   * @return $this
   */
  public function setResourceTags(array $tags);

  /**
   * Adds a resource tag.
   *
   * @param int $term_id
   *   The taxonomy term ID to add.
   *
   * @return $this
   */
  public function addResourceTag($term_id);

  /**
   * Removes a resource tag.
   *
   * @param int $term_id
   *   The taxonomy term ID to remove.
   *
   * @return $this
   */
  public function removeResourceTag($term_id);

  /**
   * Gets the creation timestamp.
   *
   * @return int
   *   Creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the creation timestamp.
   *
   * @param int $timestamp
   *   The creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the last modified timestamp.
   *
   * @return int
   *   Last modified timestamp.
   */
  public function getChangedTime();

  /**
   * Sets the last modified timestamp.
   *
   * @param int $timestamp
   *   The last modified timestamp.
   *
   * @return $this
   */
  public function setChangedTime($timestamp);

}
