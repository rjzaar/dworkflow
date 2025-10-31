<?php

namespace Drupal\dworkflow\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'dworkflow_assignment_default' widget.
 *
 * @FieldWidget(
 *   id = "dworkflow_assignment_default",
 *   label = @Translation("Workflow Assignment Selector"),
 *   field_types = {
 *     "dworkflow_assignment"
 *   }
 * )
 */
class DWorkflowAssignmentWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];

    $element['#type'] = 'container';
    $element['#attributes']['class'][] = 'dworkflow-assignment-widget';
    $element['#attributes']['class'][] = 'container-inline';

    // Get current values
    $target_type = $item->target_type ?: 'user';
    $target_id = $item->target_id;

    // Load entity if we have an ID
    $default_entity = NULL;
    if ($target_id && $target_type) {
      try {
        $default_entity = \Drupal::entityTypeManager()
          ->getStorage($target_type)
          ->load($target_id);
      }
      catch (\Exception $e) {
        // Entity not found
      }
    }

    // Check if Group module is available
    $group_module_exists = \Drupal::moduleHandler()->moduleExists('group');

    // Type selector
    $element['target_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => $this->getEntityTypeOptions($group_module_exists),
      '#default_value' => $target_type,
      '#required' => $element['#required'],
      '#ajax' => [
        'callback' => [static::class, 'ajaxUpdateAutocomplete'],
        'wrapper' => 'dworkflow-autocomplete-' . $delta,
        'event' => 'change',
      ],
    ];

    // Get the selected type (from form state if AJAX, otherwise default)
    $parents = array_merge($element['#field_parents'], [
      $items->getName(),
      $delta,
      'target_type',
    ]);
    $selected_type = $form_state->getValue($parents) ?: $target_type;

    // Entity autocomplete
    $element['target_id'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Select @type', ['@type' => $this->getEntityTypeLabel($selected_type)]),
      '#title_display' => 'invisible',
      '#target_type' => $selected_type,
      '#default_value' => $default_entity,
      '#required' => $element['#required'],
      '#prefix' => '<div id="dworkflow-autocomplete-' . $delta . '">',
      '#suffix' => '</div>',
      '#size' => 60,
      '#placeholder' => $this->t('Start typing to search...'),
    ];

    return $element;
  }

  /**
   * AJAX callback to update the autocomplete field.
   */
  public static function ajaxUpdateAutocomplete(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    
    // Navigate up to find the field container
    $parents = array_slice($triggering_element['#array_parents'], 0, -1);
    $element = $form;
    foreach ($parents as $parent) {
      $element = $element[$parent];
    }
    
    return $element['target_id'];
  }

  /**
   * Gets entity type options.
   */
  protected function getEntityTypeOptions($include_groups = FALSE) {
    $options = [
      'user' => $this->t('User'),
    ];

    if ($include_groups) {
      $options['group'] = $this->t('Group');
    }

    return $options;
  }

  /**
   * Gets label for entity type.
   */
  protected function getEntityTypeLabel($type) {
    $labels = [
      'user' => $this->t('user'),
      'group' => $this->t('group'),
    ];

    return $labels[$type] ?? $type;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    // Clean up empty values
    foreach ($values as $delta => $value) {
      if (empty($value['target_id']) || empty($value['target_type'])) {
        unset($values[$delta]);
      }
      else {
        // Ensure target_id is an integer
        $values[$delta]['target_id'] = (int) $value['target_id'];
      }
    }
    
    return $values;
  }

}
