<?php

namespace Drupal\dworkflow\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure DWorkflow settings.
 */
class DWorkflowSettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a DWorkflowSettingsForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dworkflow.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dworkflow_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dworkflow.settings');

    $form['description'] = [
      '#markup' => '<p>' . $this->t('Configure which content types can have workflow lists assigned and select the taxonomy vocabulary for resource locations.') . '</p>',
    ];

    // Content Types
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    $content_type_options = [];
    
    foreach ($node_types as $type) {
      $content_type_options[$type->id()] = $type->label();
    }

    $form['enabled_content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Content Types'),
      '#description' => $this->t('Select which content types can have workflow lists assigned to them.'),
      '#options' => $content_type_options,
      '#default_value' => $config->get('enabled_content_types') ?: [],
    ];

    // Taxonomy Vocabularies
    $vocabularies = $this->entityTypeManager->getStorage('taxonomy_vocabulary')->loadMultiple();
    $vocabulary_options = [];
    
    foreach ($vocabularies as $vocab) {
      $vocabulary_options[$vocab->id()] = $vocab->label();
    }

    $form['resource_vocabulary'] = [
      '#type' => 'select',
      '#title' => $this->t('Resource Location Vocabulary'),
      '#description' => $this->t('Select the taxonomy vocabulary to use for resource location tags. If "resource_locations" does not exist, it will be created when you save.'),
      '#options' => $vocabulary_options,
      '#default_value' => $config->get('resource_vocabulary') ?: 'resource_locations',
      '#empty_option' => $this->t('- Select a vocabulary -'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dworkflow.settings');
    
    // Filter out unchecked content types
    $enabled_types = array_filter($form_state->getValue('enabled_content_types'));
    
    $config
      ->set('enabled_content_types', array_keys($enabled_types))
      ->set('resource_vocabulary', $form_state->getValue('resource_vocabulary'))
      ->save();

    // Update field configuration for enabled content types
    $this->updateContentTypeFields(array_keys($enabled_types));

    parent::submitForm($form, $form_state);
  }

  /**
   * Updates field configuration for enabled content types.
   *
   * @param array $enabled_types
   *   Array of enabled content type machine names.
   */
  protected function updateContentTypeFields(array $enabled_types) {
    $field_storage_config = $this->entityTypeManager->getStorage('field_storage_config');
    $field_config = $this->entityTypeManager->getStorage('field_config');

    // Check if field storage exists
    $field_storage = $field_storage_config->load('node.field_workflow_list');
    
    if (!$field_storage) {
      // Create field storage
      $field_storage = $field_storage_config->create([
        'field_name' => 'field_workflow_list',
        'entity_type' => 'node',
        'type' => 'entity_reference',
        'settings' => [
          'target_type' => 'workflow_list',
        ],
        'cardinality' => 1,
      ]);
      $field_storage->save();
    }

    // Get all node types
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    foreach ($node_types as $type_id => $node_type) {
      $field = $field_config->load('node.' . $type_id . '.field_workflow_list');
      
      if (in_array($type_id, $enabled_types)) {
        // Add field if enabled and doesn't exist
        if (!$field) {
          $field = $field_config->create([
            'field_storage' => $field_storage,
            'bundle' => $type_id,
            'label' => $this->t('Workflow List'),
            'description' => $this->t('Assign a workflow list to this content.'),
            'required' => FALSE,
            'settings' => [
              'handler' => 'default',
              'handler_settings' => [
                'target_bundles' => NULL,
              ],
            ],
          ]);
          $field->save();

          // Set form display
          $form_display = $this->entityTypeManager->getStorage('entity_form_display')
            ->load('node.' . $type_id . '.default');
          
          if ($form_display) {
            $form_display->setComponent('field_workflow_list', [
              'type' => 'options_select',
              'weight' => 10,
            ])->save();
          }

          // Set view display
          $view_display = $this->entityTypeManager->getStorage('entity_view_display')
            ->load('node.' . $type_id . '.default');
          
          if ($view_display) {
            $view_display->setComponent('field_workflow_list', [
              'type' => 'entity_reference_label',
              'weight' => 10,
              'label' => 'above',
            ])->save();
          }
        }
      }
      else {
        // Remove field if disabled and exists
        if ($field) {
          $field->delete();
        }
      }
    }

    $this->messenger()->addMessage($this->t('Field configuration has been updated for enabled content types.'));
  }

}
