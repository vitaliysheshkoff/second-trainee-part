<?php

/**
 * @file
 * Contains \Drupal\trainee_user\Controller\HelloWorld.
 */

namespace Drupal\trainee_user\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route for trainee user module
 */

class UserController extends ControllerBase {

  public function users(): array
  {
    $data = \Drupal::service('trainee_user.user_manager')->getUsers(1); /*7252*/

    return [
      '#markup' => "Hello world {$data}"
    ];
  }
}
