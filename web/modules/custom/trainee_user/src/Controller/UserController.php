<?php

namespace Drupal\trainee_user\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route for trainee user module
 */
class UserController extends ControllerBase {
  public function showUserList(int $page = 1): array {
    $userList = Drupal::service('trainee_user.user_manager_service')->getList($page);
    return [
      '#theme' => 'trainee_user_list',
      '#attached' => ['library' => ['trainee_user/form']],
      '#user' => $userList,
    ];
  }
}
