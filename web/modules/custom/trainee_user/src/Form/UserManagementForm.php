<?php

/**
 * @file
 * Contains \Drupal\trainee_user\Form\UserManagementForm.
 */

namespace Drupal\trainee_user\Form;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class UserManagementForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return "user_management_form";
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if(!preg_match("/^[A-Z][a-z]+\s[A-Z][a-z]+$/", $form_state->getValue('name'))) {
      $form_state->setErrorByName('name',
        $this->t(
          "The 'User Name' attribute must be starts with uppercase,
          has a single space between first and second name"));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('User Name:'),
      '#required' => TRUE,
    );
    $form['email'] = array(
      '#type' => 'email',
      '#title' => t('Email ID:'),
      '#required' => TRUE,
    );
    $form['gender'] = array (
      '#type' => 'select',
      '#title' => ('Gender'),
      '#options' => array(
        'female' => t('female'),
        'male' => t('male'),
      ),
    );
    $form['status'] = array (
      '#type' => 'select',
      '#title' => ('Status'),
      '#options' => array(
        'female' => t('active'),
        'male' => t('inactive'),
      ),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    Drupal::messenger()->addMessage(t("New user has added:"));
    foreach ($form_state->getValues() as $key => $value) {
      Drupal::messenger()->addMessage($key . ': ' . $value);
    }
  }
}
