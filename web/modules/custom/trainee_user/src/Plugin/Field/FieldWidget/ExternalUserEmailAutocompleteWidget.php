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
 *   id = "external_user_email_autocomplete_widget",
 *   label = @Translation("User Email Autocomplete Picker"),
 *   field_types = {
 *     "user_reference_field"
 *   }
 * )
 */
class ExternalUserEmailAutocompleteWidget extends WidgetBase {

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

    try {
      $user = $this->userManager->get($items[$delta]->external_user_id);
      $default_value = $user['email'];
    }
    catch (\Throwable) {
      $default_value = '';
    }

    $element['external_user_id'] = [
      '#title' => $this->t('User email'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'trainee_user.external_user_autocomplete',
      '#autocomplite_route_parameters' => [],
      '#placeholder' => $this->t('User email'),
      '#default_value' => $default_value,
      '#element_validate' => [
        [$this, 'userValidation'],
      ],
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
  public function userValidation(array &$element, FormStateInterface $form_state) {
    $email = $element['#value'];

    // Default value if input email doesn't match API email.
    $form_state->setValueForElement($element, -1);

    $user_list = $this->userManager->getList(1);

    if (!$user_list) {
      return;
    }

    foreach ($user_list as $user) {
      if ($user['email'] === $email) {
        $form_state->setValueForElement($element, $user['id']);
        break;
      }
    }

  }

}
