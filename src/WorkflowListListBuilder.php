<?php

namespace Drupal\dworkflow;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

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
    $header['description'] = $this->t('Description');
    $header['assigned'] = $this->t('Assigned');
    $header['resources'] = $this->t('Resources');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\dworkflow\WorkflowListInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $entity->getDescription();
    
    // Count assigned entities
    $entities = $entity->getAssignedEntities();
    $user_count = count($entity->getAssignedUsers());
    $group_count = count($entity->getAssignedGroups());
    
    $assigned_parts = [];
    if ($user_count > 0) {
      $assigned_parts[] = $this->formatPlural($user_count, '1 user', '@count users');
    }
    if ($group_count > 0) {
      $assigned_parts[] = $this->formatPlural($group_count, '1 group', '@count groups');
    }
    
    $row['assigned'] = !empty($assigned_parts) ? implode(', ', $assigned_parts) : $this->t('None');
    
    // Count resource tags
    $tags = $entity->getResourceTags();
    $row['resources'] = !empty($tags) ? $this->formatPlural(count($tags), '1 resource', '@count resources') : $this->t('None');
    
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    
    // Add Quick Edit operation
    if ($entity->access('update')) {
      $operations['quick_edit'] = [
        'title' => $this->t('Quick Edit'),
        'weight' => 15,
        'url' => Url::fromRoute('dworkflow.quick_edit', [
          'workflow_list' => $entity->id(),
        ]),
      ];
    }
    
    return $operations;
  }

}
