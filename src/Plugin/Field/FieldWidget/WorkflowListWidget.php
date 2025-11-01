<?php

namespace Drupal\workflow_assignment\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Plugin implementation of the 'workflow_list_widget' widget.
 *
 * @FieldWidget(
 *   id = "workflow_list_widget",
 *   label = @Translation("Workflow List Selector"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class WorkflowListWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a WorkflowListWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Load all workflow lists.
    $workflow_storage = $this->entityTypeManager->getStorage('workflow_list');
    $workflows = $workflow_storage->loadMultiple();

    $options = [];
    foreach ($workflows as $workflow) {
      $description_parts = [$workflow->label()];
      
      // Add destination info to option label.
      $destinations = $workflow->getDestinationTags();
      if (!empty($destinations)) {
        $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
        $destination_names = [];
        foreach ($destinations as $tid) {
          $term = $term_storage->load($tid);
          if ($term) {
            $destination_names[] = $term->getName();
          }
        }
        if (!empty($destination_names)) {
          $description_parts[] = '(' . implode(', ', $destination_names) . ')';
        }
      }
      
      $options[$workflow->id()] = implode(' ', $description_parts);
    }

    // Get all current values for multi-value field
    $default_values = [];
    foreach ($items as $item) {
      if (!empty($item->value)) {
        $default_values[] = $item->value;
      }
    }

    $element['value'] = [
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $default_values,
      '#description' => $this->t('Select one or more workflows to assign to this content. View full workflow details on the Workflow tab.'),
    ];

    // Add a link to create new workflows.
    $element['create_link'] = [
      '#type' => 'markup',
      '#markup' => '<div class="description">' . $this->t('Need a new workflow? <a href="@url" target="_blank">Create one here</a>.', [
        '@url' => '/admin/structure/workflow-list/add',
      ]) . '</div>',
    ];

    // Add help text about the workflow tab
    $element['tab_info'] = [
      '#type' => 'markup',
      '#markup' => '<div class="description"><em>' . $this->t('Tip: Use the Workflow tab for a detailed table view of all assigned workflows.') . '</em></div>',
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    // Convert checkboxes array to simple values array
    $new_values = [];
    foreach ($values as $value) {
      if (isset($value['value']) && is_array($value['value'])) {
        // Filter out unchecked items (which have value 0)
        $checked_items = array_filter($value['value']);
        foreach ($checked_items as $checked_value) {
          $new_values[] = ['value' => $checked_value];
        }
      }
    }
    return $new_values;
  }

}
