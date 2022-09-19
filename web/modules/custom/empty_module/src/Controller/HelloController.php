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
    return [
      '#markup' => 'Hello world'
    ];
  }
}
