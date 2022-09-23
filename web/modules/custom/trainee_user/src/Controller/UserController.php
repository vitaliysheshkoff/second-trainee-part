<?php

namespace Drupal\trainee_user\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route for trainee user module
 */
class UserController extends ControllerBase {
  public function showUserList(int $page = 1): array {
    Drupal::service('page_cache_kill_switch')->trigger();
    $userList = Drupal::service('trainee_user.user_manager_service')->getList($page);
    return [
      '#theme' => 'trainee_user_list',
      '#user' => $userList,
    ];
  }
}
