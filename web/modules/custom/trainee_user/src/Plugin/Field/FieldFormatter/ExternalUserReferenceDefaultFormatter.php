<?php

namespace Drupal\trainee_user\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the formatter for 'external_user' fields.
 *
 * @FieldFormatter(
 *   id = "external_user_reference_default_formatter",
 *   label = @Translation("External user"),
 *   field_types = {
 *     "user_reference_field"
 *   }
 * )
 */
class ExternalUserReferenceDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => strtoupper("Email: $item->email</br> Name : $item->name"),
      ];
    }

    return $element;
  }

}
