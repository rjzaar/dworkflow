<?php

namespace Drupal\workflow_assignment;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of Workflow Lists.
 */
class WorkflowListListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['id'] = $this->t('Machine name');
    $header['description'] = $this->t('Description');
    $header['users'] = $this->t('Users');
    $header['destinations'] = $this->t('Destinations');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\workflow_assignment\Entity\WorkflowList $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    
    // Description (truncated).
    $description = $entity->getDescription();
    $row['description'] = $description ? \Drupal::service('text.truncate')->truncate($description, 60) : '-';
    
    // User count.
    $users = $entity->getAssignedUsers();
    $row['users'] = count($users) . ' ' . $this->formatPlural(count($users), 'user', 'users');
    
    // Destination locations.
    $destinations = $entity->getDestinationTags();
    if (!empty($destinations)) {
      $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
      $destination_names = [];
      foreach ($destinations as $tid) {
        $term = $term_storage->load($tid);
        if ($term) {
          $destination_names[] = $term->getName();
        }
      }
      $row['destinations'] = implode(', ', $destination_names);
    }
    else {
      $row['destinations'] = '-';
    }
    
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    
    // Add quick edit operation.
    $operations['quick_edit'] = [
      'title' => $this->t('Quick Edit'),
      'weight' => 15,
      'url' => Url::fromRoute('workflow_assignment.quick_edit', [
        'workflow_list' => $entity->id(),
      ]),
    ];
    
    return $operations;
  }

}
