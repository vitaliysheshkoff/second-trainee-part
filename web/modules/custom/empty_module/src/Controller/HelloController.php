<?php

/**
 * @file
 * Contains \Drupal\empty_module\Controller\HelloWorld.
 */

namespace Drupal\empty_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route for empty module
 */

class HelloController extends ControllerBase {

  public function hello(): array
  {

   // $data = \Drupal::service('trainee_user.user_manager')->updateUser(7252, 'Has Fs','male','654321123456pavlo@mail.com', 'inactive');
   // $data = \Drupal::service('trainee_user.user_manager')->getUsers(1); /*7252*/
    $data = \Drupal::service('trainee_user.user_manager')->deleteUser(7252); /*7252*/

    return [
      '#markup' => "Hello world {$data}"
    ];
  }

  public function users(): array
  {

    // $data = \Drupal::service('trainee_user.user_manager')->updateUser(7252, 'Has Fs','male','654321123456pavlo@mail.com', 'inactive');
    $data = \Drupal::service('trainee_user.user_manager')->getUsers(1); /*7252*/
   // $data = \Drupal::service('trainee_user.user_manager')->deleteUser(7252); /*7252*/

    return [
      '#markup' => "Hello world {$data}"
    ];
  }
}
