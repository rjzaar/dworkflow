<?php

namespace Drupal\dworkflow;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Workflow List entities.
 */
interface WorkflowListInterface extends ConfigEntityInterface {

  /**
   * Gets the workflow description.
   *
   * @return string
   *   The workflow description.
   */
  public function getDescription();

  /**
   * Sets the workflow description.
   *
   * @param string $description
   *   The workflow description.
   *
   * @return $this
   */
  public function setDescription($description);

  /**
   * Gets all assigned entities (users and groups).
   *
   * @return array
   *   Array of entity information with keys:
   *   - entity_type: 'user' or 'group'
   *   - entity_id: The entity ID
   *   - entity: The loaded entity object
   */
  public function getAssignedEntities();

  /**
   * Gets assigned user IDs only.
   *
   * @return array
   *   Array of user IDs.
   */
  public function getAssignedUsers();

  /**
   * Gets assigned group IDs only.
   *
   * @return array
   *   Array of group IDs.
   */
  public function getAssignedGroups();

  /**
   * Sets all assigned entities.
   *
   * @param array $entities
   *   Array of entities with 'target_type' and 'target_id' keys.
   *
   * @return $this
   */
  public function setAssignedEntities(array $entities);

  /**
   * Adds an entity to the workflow.
   *
   * @param string $entity_type
   *   The entity type ('user' or 'group').
   * @param int $entity_id
   *   The entity ID.
   *
   * @return $this
   */
  public function addAssignedEntity($entity_type, $entity_id);

  /**
   * Removes an entity from the workflow.
   *
   * @param string $entity_type
   *   The entity type ('user' or 'group').
   * @param int $entity_id
   *   The entity ID.
   *
   * @return $this
   */
  public function removeAssignedEntity($entity_type, $entity_id);

  /**
   * Adds a user to the workflow.
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return $this
   */
  public function addAssignedUser($user_id);

  /**
   * Adds a group to the workflow.
   *
   * @param int $group_id
   *   The group ID.
   *
   * @return $this
   */
  public function addAssignedGroup($group_id);

  /**
   * Removes a user from the workflow.
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return $this
   */
  public function removeAssignedUser($user_id);

  /**
   * Removes a group from the workflow.
   *
   * @param int $group_id
   *   The group ID.
   *
   * @return $this
   */
  public function removeAssignedGroup($group_id);

  /**
   * Gets resource location tags.
   *
   * @return array
   *   Array of taxonomy term IDs.
   */
  public function getResourceTags();

  /**
   * Sets resource location tags.
   *
   * @param array $tags
   *   Array of taxonomy term IDs.
   *
   * @return $this
   */
  public function setResourceTags(array $tags);

  /**
   * Adds a resource location tag.
   *
   * @param int $tag_id
   *   The taxonomy term ID.
   *
   * @return $this
   */
  public function addResourceTag($tag_id);

  /**
   * Removes a resource location tag.
   *
   * @param int $tag_id
   *   The taxonomy term ID.
   *
   * @return $this
   */
  public function removeResourceTag($tag_id);

  /**
   * Gets the workflow creation timestamp.
   *
   * @return int
   *   Creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the workflow creation timestamp.
   *
   * @param int $timestamp
   *   The creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the workflow last changed timestamp.
   *
   * @return int
   *   Last changed timestamp.
   */
  public function getChangedTime();

  /**
   * Sets the workflow last changed timestamp.
   *
   * @param int $timestamp
   *   The last changed timestamp.
   *
   * @return $this
   */
  public function setChangedTime($timestamp);

}
