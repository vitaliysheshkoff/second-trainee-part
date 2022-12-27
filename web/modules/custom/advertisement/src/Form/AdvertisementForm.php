<?php

namespace Drupal\advertisement\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a advertisement form.
 */
class AdvertisementForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advertisement_advertisement';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The title of the advertisement'),
      '#required' => TRUE,
    ];

    $form['advertisement_image'] = [
      '#type' => 'managed_file',
      '#title' => 'Advertisement image',
      '#name' => 'advertisement_image',
      '#description' => $this->t('The image of advertisement'),
     // '#default_value' => [$config->get('my_file')],
      '#upload_location' => 'public://advertisement_img',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (mb_strlen($form_state->getValue('message')) < 10) {
      $form_state->setErrorByName('message', $this->t('Message should be at least 10 characters.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}
