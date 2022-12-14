<?php

namespace Drupal\trainee_empty_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route for trainee empty module.
 */
class HelloController extends ControllerBase {

  /**
   * Provides to show hello message.
   */
  public function hello(): array {
    return [
      '#markup' => "Hello world!",
    ];
  }

}
