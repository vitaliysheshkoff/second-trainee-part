<?php

namespace Drupal\trainee_user\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'external_user' field type.
 *
 * @FieldType(
 *   id = "user_reference_field",
 *   label = @Translation("External user"),
 *   module = "trainee_user",
 *   description = @Translation("External user"),
 *   category = @Translation("External user"),
 *   default_widget = "external_user_reference_widget",
 *   default_formatter = "external_user_reference_default_formatter"
 * )
 */
class ExternalUserReference extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'email' => [
          'description' => 'The external user email',
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
        'name' => [
          'description' => 'The external user name',
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];

    $properties['email'] = DataDefinition::create('string')->setLabel(t('User Email'));
    $properties['name'] = DataDefinition::create('string')->setLabel(t('User Name'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return empty($this->email);
  }

}
