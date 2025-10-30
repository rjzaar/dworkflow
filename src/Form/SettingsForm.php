<?php

namespace Drupal\workflow_assignment\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Workflow Assignment settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a SettingsForm object.
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
    return ['workflow_assignment.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workflow_assignment_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('workflow_assignment.settings');

    // Get all content types
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    $content_type_options = [];
    
    foreach ($node_types as $type) {
      $content_type_options[$type->id()] = $type->label();
    }

    $form['allowed_content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Content Types'),
      '#description' => $this->t('Select which content types can have workflow lists assigned.'),
      '#options' => $content_type_options,
      '#default_value' => $config->get('allowed_content_types') ?: ['page', 'topic', 'event'],
    ];

    // Get all vocabularies
    $vocabularies = $this->entityTypeManager->getStorage('taxonomy_vocabulary')->loadMultiple();
    $vocabulary_options = [];
    
    foreach ($vocabularies as $vocabulary) {
      $vocabulary_options[$vocabulary->id()] = $vocabulary->label();
    }

    $form['resource_vocabulary'] = [
      '#type' => 'select',
      '#title' => $this->t('Resource Location Vocabulary'),
      '#description' => $this->t('Select the taxonomy vocabulary to use for resource location tags.'),
      '#options' => $vocabulary_options,
      '#default_value' => $config->get('resource_vocabulary') ?: 'resource_locations',
      '#required' => TRUE,
    ];

    $form['info'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Note: Changing these settings will affect which content types can be assigned workflow lists.') . '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $allowed_types = array_filter($form_state->getValue('allowed_content_types'));

    $this->config('workflow_assignment.settings')
      ->set('allowed_content_types', array_values($allowed_types))
      ->set('resource_vocabulary', $form_state->getValue('resource_vocabulary'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
