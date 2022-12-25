<?php

namespace Drupal\trainee_user\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form to handle Api parameters.
 */
class UserApiConfigForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'trainee_user.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'trainee_user_api_config_page';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config(static::SETTINGS);

    $form['api_base_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Base URL'),
      '#decription' => $this->t('the API Base URL'),
      '#required' => TRUE,
      '#default_value' => $config->get('api_base_url') ?? '',
    ];

    $form['header_accept'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Header Accept'),
      '#decription' => $this->t('header parameter'),
      '#required' => TRUE,
      '#default_value' => $config->get('header_accept') ?? '',
    ];

    $form['header_content_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Header Content-Type'),
      '#decription' => $this->t('header parameter'),
      '#required' => TRUE,
      '#default_value' => $config->get('header_content_type') ?? '',
    ];

    $form['api_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Access-Token'),
      '#decription' => $this->t('the API Access-Token that will be used to access the API'),
      '#required' => TRUE,
      '#default_value' => $config->get('api_token') ?? '',
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
    $this->config(static::SETTINGS)
      ->set('api_base_url', $form_state->getValue('api_base_url'))
      ->set('api_token', $form_state->getValue('api_token'))
      ->set('header_content_type', $form_state->getValue('header_content_type'))
      ->set('header_accept', $form_state->getValue('header_accept'))
      ->save();

    $this->messenger()
      ->addMessage($this->t('New configuration has been saved'));

    parent::submitForm($form, $form_state);
  }

}
