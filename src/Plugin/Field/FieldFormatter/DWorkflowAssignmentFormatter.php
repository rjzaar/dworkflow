<?php

namespace Drupal\dworkflow\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'dworkflow_assignment_default' formatter.
 *
 * @FieldFormatter(
 *   id = "dworkflow_assignment_default",
 *   label = @Translation("Workflow Assignment List"),
 *   field_types = {
 *     "dworkflow_assignment"
 *   }
 * )
 */
class DWorkflowAssignmentFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    if ($items->isEmpty()) {
      return $elements;
    }

    $assignments = [];

    foreach ($items as $delta => $item) {
      $entity = $item->getEntity();
      
      if ($entity) {
        $assignments[] = [
          'title' => $item->title ?? '',
          'type' => $item->target_type,
          'entity' => $entity,
          'label' => $entity->label(),
          'url' => $entity->toUrl()->toString(),
          'comment' => $item->comment ?? '',
        ];
      }
    }

    if (!empty($assignments)) {
      $elements[0] = [
        '#theme' => 'dworkflow_assignment_list',
        '#assignments' => $assignments,
        '#attached' => [
          'library' => ['dworkflow/dworkflow'],
        ],
      ];
    }

    return $elements;
  }

}
