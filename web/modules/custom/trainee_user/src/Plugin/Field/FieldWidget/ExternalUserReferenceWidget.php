<?php

namespace Drupal\trainee_user\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'external_user_reference' widget.
 *
 * @FieldWidget(
 *   id = "external_user_reference_widget",
 *   label = @Translation("External user"),
 *   field_types = {
 *     "user_reference_field"
 *   }
 * )
 */
class ExternalUserReferenceWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {

    $default_value_email = $items[$delta]->email ?? '';

    $element['email'] = [
      '#title' => $this->t('Api user email'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'trainee_user.external_user_autocomplete',
      '#autocomplite_route_parameters' => [/*'page' => 1*/],
      '#placeholder' => $this->t('User email'),
      '#default_value' => $default_value_email,
      '#element_validate' => [
        [$this, 'userValidation'],
      ],
    ];

    $names = [];
    $user_list = \Drupal::service('trainee_user.user_manager_service')->getList(1);
    foreach ($user_list as $user) {
      $names[] = $user['name'];
    }

    $element['name'] = [
      '#title' => $this->t('Api user name'),
      '#type' => 'select',
     // '#autocomplete_route_name' => 'trainee_user.external_user_autocomplete',
      '#options' => $names,
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
