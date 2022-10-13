<?php

namespace Drupal\trainee_user\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

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
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {

    $default_value = $items[$delta]->id ?? '';

    $element['id'] = [
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
  public function userValidation(array $element, FormStateInterface $form_state) {
  }

}
