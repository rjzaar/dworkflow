<?php

namespace Drupal\dworkflow\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type for referencing users or groups.
 *
 * @FieldType(
 *   id = "dworkflow_assignment",
 *   label = @Translation("Workflow Assignment"),
 *   description = @Translation("References a user or group for workflow assignment."),
 *   default_widget = "dworkflow_assignment_default",
 *   default_formatter = "dworkflow_assignment_default",
 *   cardinality = \Drupal\Core\Field\FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED
 * )
 */
class DWorkflowAssignmentItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['target_type'] = DataDefinition::create('string')
      ->setLabel(t('Entity type'))
      ->setDescription(t('The entity type (user or group).'))
      ->setRequired(TRUE);

    $properties['target_id'] = DataDefinition::create('integer')
      ->setLabel(t('Entity ID'))
      ->setDescription(t('The entity ID.'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'target_type' => [
          'type' => 'varchar',
          'length' => 32,
          'not null' => TRUE,
        ],
        'target_id' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
      ],
      'indexes' => [
        'target_type' => ['target_type'],
        'target_id' => ['target_id'],
        'target_type_id' => ['target_type', 'target_id'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $target_type = $this->get('target_type')->getValue();
    $target_id = $this->get('target_id')->getValue();
    return empty($target_type) || empty($target_id);
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'target_id';
  }

  /**
   * Loads the referenced entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The referenced entity or NULL if not found.
   */
  public function getEntity() {
    $target_type = $this->get('target_type')->getValue();
    $target_id = $this->get('target_id')->getValue();

    if (empty($target_type) || empty($target_id)) {
      return NULL;
    }

    try {
      return \Drupal::entityTypeManager()
        ->getStorage($target_type)
        ->load($target_id);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

}
