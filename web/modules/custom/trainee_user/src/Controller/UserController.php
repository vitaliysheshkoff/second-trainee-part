<?php

namespace Drupal\trainee_user\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route for trainee user module
 */
class UserController extends ControllerBase{
  public function users(): array{
    $data = Drupal::service('trainee_user.user_manager')->getList(20)[0]->name;
    return [
      '#markup' => $data
    ];
  }
}
