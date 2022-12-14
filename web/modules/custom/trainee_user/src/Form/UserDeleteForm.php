<?php

namespace Drupal\trainee_user\Form;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\trainee_user\UserManagerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Delete form for trainee_user module.
 */
class UserDeleteForm extends ConfirmFormBase {

  /**
   * The user manager.
   *
   * @var \Drupal\trainee_user\UserManagerService
   */
  protected UserManagerService $userManager;

  /**
   * UserDeleteForm constructor.
   *
   * @param \Drupal\trainee_user\UserManagerService $user_manager
   *   The user manager.
   */
  public function __construct(UserManagerService $user_manager) {
    $this->userManager = $user_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): UserDeleteForm {
    return new static(
      $container->get('trainee_user.user_manager_service'),
    );
  }

  /**
   * The id of deleting user.
   *
   * @var int
   */
  private int $id;

  /**
   * The page of deleting user.
   *
   * @var int
   */
  private int $page;

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): string {
    return $this->t('Delete this user?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return Url::fromRoute('trainee_user.user_list')
      ->setRouteParameters(['page' => $this->page]);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'user_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): TranslatableMarkup {
    try {
      $targetUser = $this->userManager->get($this->id);
    }
    catch (\Throwable $exception) {
      $error_message = preg_replace('/`[\s\S]+?`/', '', $exception->getMessage(), 1);
      $this->messenger()->addMessage($error_message, 'error');
      return $this->t('Unable to delete this user.');
    }
    return $this->t("Are you sure do you want to delete this user? <br> ID: @userId <br>NAME: @userName <br>EMAIL: @userEmail <br>GENDER: @userGender <br> STATUS: @userStatus",
      [
        '@userId' => $targetUser['id'],
        '@userName' => $targetUser['name'],
        '@userEmail' => $targetUser['email'],
        '@userGender' => $targetUser['gender'],
        '@userStatus' => $targetUser['status'],
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText(): TranslatableMarkup {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText(): TranslatableMarkup {
    return $this->t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $id = NULL): array {

    $this->id = $id;

    if ($this->getRequest()->get('page') !== NULL) {
      $this->page = $this->getRequest()->get('page');
    }
    else {
      $this->page = 1;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $this->userManager->delete($this->id);
      $this->messenger()
        ->addMessage($this->t('User has been successfully deleted'));
    }
    catch (\Throwable $exception) {
      $error_message = preg_replace('/`[\s\S]+?`/', '', $exception->getMessage(), 1);
      $this->messenger()->addMessage($error_message, 'error');
    }

    $url = Url::fromRoute('trainee_user.user_list')
      ->setRouteParameters(['page' => $this->page]);
    $form_state->setRedirectUrl($url);
  }

}
