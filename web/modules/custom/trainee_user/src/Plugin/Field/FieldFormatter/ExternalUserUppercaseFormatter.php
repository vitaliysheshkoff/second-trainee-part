<?php

namespace Drupal\trainee_user\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\trainee_user\UserManagerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the formatter for 'external_user' fields.
 *
 * @FieldFormatter(
 *   id = "external_user_uppercase_formatter",
 *   label = @Translation("User info in uppercase(NAME and EMAIL)"),
 *   field_types = {
 *     "user_reference_field"
 *   }
 * )
 */
class ExternalUserUppercaseFormatter extends FormatterBase {

  /**
   * The user manager.
   *
   * @var \Drupal\trainee_user\UserManagerService
   */
  protected UserManagerService $userManager;

  /**
   * Constructs a new ExternalUserUppercaseFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\trainee_user\UserManagerService $user_manager
   *   The user manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, UserManagerService $user_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->userManager = $user_manager;
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
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('trainee_user.user_manager_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $delta => $item) {
      if (isset($item->external_user_id)) {
        try {
          $user = $this->userManager->get($item->external_user_id);
        }
        catch (\Throwable $exception) {
          $error_message = preg_replace('/`[\s\S]+?`/', '', $exception->getMessage(), 1);
          $this->messenger()->addMessage($error_message, 'error');

          return $element;
        }
        $element[$delta] = [
          '#type' => 'markup',
          '#markup' => "Name: " . strtoupper($user['name']) . "</br>" . "Email: " . strtoupper($user['email']),
        ];
      }
    }

    return $element;
  }

}
