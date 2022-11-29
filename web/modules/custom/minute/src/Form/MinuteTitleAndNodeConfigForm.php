<?php

namespace Drupal\minute\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to handle minute module configurations.
 */
class MinuteTitleAndNodeConfigForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'module.settings';

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * MinuteTitleAndNodeConfigForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      self::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'minute_minute_title_and_node_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config(static::SETTINGS);

    $form['even_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Even title in node'),
      '#decription' => $this->t('text for even title'),
      '#required' => TRUE,
      '#default_value' => $config->get('even_title') ?? '',
    ];

    $form['odd_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Odd title in node'),
      '#decription' => $this->t('text for odd title'),
      '#required' => TRUE,
      '#default_value' => $config->get('odd_title') ?? '',
    ];

    $form['odd_entity'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => $this->t('Odd Node'),
      '#description' => $this->t('Select Node that is showing Odd context'),
      '#required' => TRUE,
      '#default_value' => $this->entityTypeManager->getStorage('node')
        ->load($config->get('odd_entity')[0]['target_id']),
      '#tags' => TRUE,
      '#selection_settings' => [
        'target_bundles' => ['page', 'article'],
      ],
    ];

    $form['even_entity'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => $this->t('Even Node'),
      '#description' => $this->t('Select Node that is showing Odd context'),
      '#required' => TRUE,
      '#default_value' => $this->entityTypeManager->getStorage('node')
        ->load($config->get('even_entity')[0]['target_id']),
      '#tags' => TRUE,
      '#selection_settings' => [
        'target_bundles' => ['page', 'article'],
      ],
    ];

    $form['action']['#type'] = 'action';
    $form['action']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config(self::SETTINGS)
      ->set('even_title', $form_state->getValue('even_title'))
      ->set('odd_title', $form_state->getValue('odd_title'))
      ->set('even_entity', $form_state->getValue('even_entity'))
      ->set('odd_entity', $form_state->getValue('odd_entity'))
      ->save();

    $this->messenger()
      ->addMessage($this->t('New configuration has been saved'));

  }

}
