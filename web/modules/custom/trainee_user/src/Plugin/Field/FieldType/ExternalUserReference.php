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
 *   default_widget = "external_user_name_widget",
 *   default_formatter = "external_user_default_formatter"
 * )
 */
class ExternalUserReference extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'external_user_id' => [
          'description' => 'The external user id',
          'unsigned' => FALSE,
          'type' => 'int',
          'size' => 'normal',
          'not null' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];
    $properties['external_user_id'] = DataDefinition::create('integer')
      ->setLabel(t('External user id'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return empty($this->external_user_id);
  }

}
