<?php

namespace Drupal\trainee_user\Form;

use Drupal;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Throwable;

class UserDeleteForm extends ConfirmFormBase {
  private int $id;

  public function getQuestion(): string {
    return 'Delete this user?';
  }

  public function getCancelUrl(): Url {
    return Url::fromRoute('trainee_user.user_list')
      ->setRouteParameters(array('page' => 1));
  }

  public function getFormId(): string {
    return 'user_delete_form';
  }

  public function getDescription(): Drupal\Core\StringTranslation\TranslatableMarkup {
    try {
      $targetUser = Drupal::service('trainee_user.user_manager_service')->get($this->id);
    } catch (Throwable $exception) {
      $error_message = preg_replace('/`[\s\S]+?`/', '', $exception->getMessage(), 1);
      Drupal::messenger()->addMessage(t($error_message), 'error');
      return t('unable to delete this user');
    }
    return t('Are you sure do you want to delete this user?' . '<br>'
      . 'ID: ' . $targetUser['id'] . '<br>'
      . 'NAME: ' . $targetUser['name'] . '<br>'
      . 'EMAIL: ' . $targetUser['email'] . '<br>'
      . 'GENDER: ' . $targetUser['gender'] . '<br>'
      . 'STATUS: ' . $targetUser['status'] . '<br>'
    );
  }

  public function getConfirmText(): Drupal\Core\StringTranslation\TranslatableMarkup {
    return t('Delete');
  }

  public function getCancelText(): Drupal\Core\StringTranslation\TranslatableMarkup {
    return t('Cancel');
  }

  public function buildForm(array $form, FormStateInterface $form_state, int $id = NULL): array {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      Drupal::service('trainee_user.user_manager_service')->delete($this->id);
      Drupal::messenger()->addMessage(t('User has been successfully deleted'));
    } catch (Throwable $exception) {
      $error_message = preg_replace('/`[\s\S]+?`/', '', $exception->getMessage(), 1);
      Drupal::messenger()->addMessage(t($error_message), 'error');
    }

    $url = Url::fromRoute('trainee_user.user_list')
      ->setRouteParameters(array('page' => 1));
    $form_state->setRedirectUrl($url);
  }

}
