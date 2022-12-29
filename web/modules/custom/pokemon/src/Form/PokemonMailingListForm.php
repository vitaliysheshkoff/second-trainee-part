<?php

namespace Drupal\pokemon\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form to configure emailing list.
 */
class PokemonMailingListForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'pokemon_mailing_list_form.settings';

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
    return 'pokemon_mailing_list_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config(static::SETTINGS);

    $form['mailing_list'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mailing list'),
      '#decription' => $this->t('The mailing list for updating actions'),
      '#required' => TRUE,
      '#default_value' => $config->get('mailing_list') ?? '',
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

    $this->config(static::SETTINGS)
      ->set('mailing_list', $form_state->getValue('mailing_list'))
      ->save();

    $this->messenger()
      ->addMessage($this->t('New configuration has been saved'));

  }

}
