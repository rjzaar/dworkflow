<?php

namespace Drupal\workflow_assignment;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Workflow List entities.
 */
class WorkflowListListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['id'] = $this->t('Machine name');
    $header['assigned_users'] = $this->t('Users');
    $header['assigned_groups'] = $this->t('Groups');
    $header['resource_tags'] = $this->t('Resource Locations');
    $header['changed'] = $this->t('Last Modified');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\workflow_assignment\WorkflowListInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    
    // Count assigned users
    $users = $entity->getAssignedUsers();
    $row['assigned_users'] = count($users) . ' ' . $this->formatPlural(count($users), 'user', 'users');
    
    // Count assigned groups
    $groups = $entity->getAssignedGroups();
    $row['assigned_groups'] = count($groups) . ' ' . $this->formatPlural(count($groups), 'group', 'groups');
    
    // Count resource tags
    $tags = $entity->getResourceTags();
    $row['resource_tags'] = count($tags) . ' ' . $this->formatPlural(count($tags), 'location', 'locations');
    
    // Format changed time
    $row['changed'] = \Drupal::service('date.formatter')->format($entity->getChangedTime(), 'short');
    
    return $row + parent::buildRow($entity);
  }

}
