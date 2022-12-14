<?php

namespace Drupal\trainee_user\Form;

use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\trainee_user\UserManagerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adding form for trainee_user module.
 */
class UserManagementForm extends FormBase {

  /**
   * The user manager.
   *
   * @var \Drupal\trainee_user\UserManagerService
   */
  protected UserManagerService $userManager;

  /**
   * The email validator.
   *
   * @var \Drupal\Component\Utility\EmailValidator
   */
  protected EmailValidator $emailValidator;

  /**
   * UserManagementForm constructor.
   *
   * @param \Drupal\trainee_user\UserManagerService $user_manager
   *   The user manager.
   * @param \Drupal\Component\Utility\EmailValidator $emailValidator
   *   The email validator.
   */
  public function __construct(UserManagerService $user_manager, EmailValidator $emailValidator) {
    $this->userManager = $user_manager;
    $this->emailValidator = $emailValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): UserManagementForm {
    return new static(
     $container->get('trainee_user.user_manager_service'),
     $container->get('email.validator'),
    );
  }

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
    $email = $form_state->getValue('email');
    if (!$this->emailValidator->isValid($email)) {
      $form_state->setErrorByName('email',
        $this->t('The email address %mail is not valid.', ['%mail' => $email]));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    if ($this->getRequest()->get('id') !== NULL) {
      try {
        $user = $this->userManager->get($this->getRequest()->get('id'));
      }
      catch (\Throwable $exception) {
        $error_message = preg_replace('/`[\s\S]+?`/', '',
          $exception->getMessage(), 1);
        $this->messenger()->addMessage($error_message, 'error');
        $user = NULL;
      }
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('User Name:'),
      '#required' => TRUE,
      '#default_value' => (isset($user['name']) && $this->getRequest()
        ->get('id')) ? $user['name'] : '',
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email:'),
      '#required' => TRUE,
      '#default_value' => (isset($user['email']) && $this->getRequest()
        ->get('id')) ? $user['email'] : '',
    ];
    $form['gender'] = [
      '#type' => 'select',
      '#title' => ('Gender'),
      '#options' => [
        'female' => $this->t('female'),
        'male' => $this->t('male'),
      ],
      '#default_value' => (isset($user['gender']) && $this->getRequest()
        ->get('id')) ? $user['gender'] : '',
    ];
    $form['status'] = [
      '#type' => 'select',
      '#title' => ('Status'),
      '#options' => [
        'active' => $this->t('active'),
        'inactive' => $this->t('inactive'),
      ],
      '#default_value' => (isset($user['status']) && $this->getRequest()
        ->get('id')) ? $user['status'] : '',
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    if ($this->getRequest()->get('page') !== NULL) {
      $form['action']['cancel'] = [
        '#markup' => Link::fromTextAndUrl($this->t('back to user list'), $this->getUrl())
          ->toString(),
      ];
    }

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
    $new_user = NULL;

    try {
      if ($this->getRequest()->get('id') !== NULL) {
        $new_user = $this->userManager->update($this->getRequest()
          ->get('id'), $user);
        $this->messenger()->addMessage($this->t('User has been updated successfully'));
      }
      else {
        $new_user = $this->userManager->create($user);
        $this->messenger()->addMessage($this->t('New user has been created successfully'));
      }
    }
    catch (\Throwable $exception) {
      $error_message = preg_replace('/`[\s\S]+?`/', '',
        $exception->getMessage(), 1);
    }

    if ($new_user === NULL) {
      $this->messenger()->addMessage($error_message, 'error');
    }
    else {
      $this->messenger()->addMessage($this->t('User id: @newUserId',
        ['@newUserId' => $new_user['id']]));
      $this->messenger()
        ->addMessage($this->t('User name: @newUserName',
          ['@newUserName' => $new_user['name']]));
      $this->messenger()
        ->addMessage($this->t('Email: @newUserEmail',
          ['@newUserEmail' => $new_user['email']]));
      $this->messenger()
        ->addMessage($this->t('Gender: @newUserGender',
          ['@newUserGender' => $new_user['gender']]));
      $this->messenger()
        ->addMessage($this->t('Status: @newUserStatus',
          ['@newUserStatus' => $new_user['status']]));
    }

    $form_state->setRedirectUrl($this->getUrl());
  }

  /**
   * Provides to get redirect url.
   *
   * @return \Drupal\Core\Url
   *   Redirect url.
   */
  public function getUrl(): Url {
    return Url::fromRoute('trainee_user.user_list')
      ->setRouteParameters([
        'page' => $this->getRequest()
          ->get('page') ? $this->getRequest()
          ->get('page') : 1,
      ]);
  }

}
