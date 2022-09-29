<?php

/**
 * @file
 * Contains \Drupal\trainee_user\Controller\UserController.
 */

namespace Drupal\trainee_user\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class UserController.
 */
class UserController extends ControllerBase {

  /**
   * Provides to show user list theme.
   *
   * @param int $page
   *   The page.
   *
   * @return array
   *   User list theme.
   */
  public function showUserList(int $page = 1): array {
    Drupal::service('page_cache_kill_switch')->trigger();
    $userList = Drupal::service('trainee_user.user_manager_service')
      ->getList($page);

    return [
      '#theme' => 'trainee_user_list',
      '#users' => $userList,
      '#attributes' => [
        'button' => [
          'class' => 'button button--link',
        ],
      ],
      '#button_text' => [
        'add' => 'add new user',
        'delete' => 'delete',
        'edit' => 'edit',
      ],
      '#table' => [
        'ID',
        'NAME',
        'EMAIL',
        'GENDER',
        'STATUS',
        'ACTION',
      ],
      '#routes' => [
        'add' => 'trainee_user.management_form',
        'delete' => 'trainee_user.delete_form',
      ],
      '#page' => $page,
      '#attached' => ['library' => ['trainee_user/table-style']],
    ];
  }

}
