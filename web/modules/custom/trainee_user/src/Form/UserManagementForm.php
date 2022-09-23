<?php

/**
 * @file
 * Contains \Drupal\trainee_user\Form\UserManagementForm.
 */

namespace Drupal\trainee_user\Form;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Throwable;

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
   /* if (!preg_match("/^[A-Z][a-z]+\s[A-Z][a-z]+$/", $form_state->getValue('name'))) {
      $form_state->setErrorByName('name',
        $this->t(
          "The 'User Name' attribute must be starts with uppercase,
          has a single space between first and second name"));
    }*/

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    if (isset($_GET['id'])) {
      try {
        $user = Drupal::service('trainee_user.user_manager_service')->get($_GET['id']);
      } catch (Throwable $exception) {
        $error_message = preg_replace('/`[\s\S]+?`/', '', $exception->getMessage(), 1);
        Drupal::messenger()->addMessage(t($error_message), 'error');
        $user = null;
      }
    }

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('User Name:'),
      '#required' => TRUE,
      '#default_value' => (isset($user['name']) && $_GET['id']) ? $user['name'] : ''
    );
    $form['email'] = array(
      '#type' => 'email',
      '#title' => t('Email:'),
      '#required' => TRUE,
      '#default_value' => (isset($user['email']) && $_GET['id']) ? $user['email'] : ''
    );
    $form['gender'] = array(
      '#type' => 'select',
      '#title' => ('Gender'),
      '#options' => array(
        'female' => t('female'),
        'male' => t('male'),
      ),
      '#default_value' => (isset($user['gender']) && $_GET['id']) ? $user['gender'] : ''
    );
    $form['status'] = array(
      '#type' => 'select',
      '#title' => ('Status'),
      '#options' => array(
        'active' => t('active'),
        'inactive' => t('inactive'),
      ),
      '#default_value' => (isset($user['status']) && $_GET['id']) ? $user['status'] : ''
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

    $user['status'] = $form_state->getValue('status');
    $user['name'] = $form_state->getValue('name');
    $user['email'] = $form_state->getValue('email');
    $user['gender'] = $form_state->getValue('gender');

    $error_message = '';
    $answer = '';
    $newUser = null;

    try {
      if (isset($_GET['id'])) {
        $newUser = Drupal::service('trainee_user.user_manager_service')->update($_GET['id'], $user);
        $answer = "New user has been updated successfully";
      } else {
        $newUser = Drupal::service('trainee_user.user_manager_service')->create($user);
        $answer = "New user has been created successfully";
      }
    } catch (Throwable $exception) {
      $error_message = preg_replace('/`[\s\S]+?`/', '', $exception->getMessage(), 1);
    }

    if ($newUser === null) {
      Drupal::messenger()->addMessage(t($error_message), 'error');
    } else {
      Drupal::messenger()->addMessage(t($answer));
      Drupal::messenger()->addMessage('User id' . ': ' . $newUser['id']);
      Drupal::messenger()->addMessage('User name' . ': ' . $newUser['name']);
      Drupal::messenger()->addMessage('Email' . ': ' . $newUser['email']);
      Drupal::messenger()->addMessage('Gender' . ': ' . $newUser['gender']);
      Drupal::messenger()->addMessage('Status' . ': ' . $newUser['status']);
    }

    $url = Url::fromRoute('trainee_user.user_list')
      ->setRouteParameters(array('page' => 1));
    $form_state->setRedirectUrl($url);
  }

}
