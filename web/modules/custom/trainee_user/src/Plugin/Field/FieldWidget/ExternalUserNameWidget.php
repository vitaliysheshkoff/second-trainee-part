<?php

namespace Drupal\trainee_user\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\trainee_user\UserManagerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'external_user_reference' widget.
 *
 * @FieldWidget(
 *   id = "external_user_name_widget",
 *   label = @Translation("User Name Picker"),
 *   field_types = {
 *     "user_reference_field"
 *   }
 * )
 */
class ExternalUserNameWidget extends WidgetBase {

  /**
   * The user manager.
   *
   * @var \Drupal\trainee_user\UserManagerService
   */
  protected UserManagerService $userManager;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, UserManagerService $user_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->userManager = $user_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['third_party_settings'], $container->get('trainee_user.user_manager_service'));
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {

    $default_value = $items[$delta]->external_user_id ?? '';

    $names = [];
    $user_list = $this->userManager->getList(1);
    foreach ($user_list as $user) {
      $names[$user['id']] = $user['name'];
    }

    $element['external_user_id'] = [
      '#title' => $this->t('User name'),
      '#type' => 'select',
      '#options' => $names,
      '#default_value' => $default_value,
    ];

    return $element;
  }

  /**
   * Provides user validation.
   *
   * @param array $element
   *   The array of element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function userValidation(array $element, FormStateInterface $form_state) {
  }

}
