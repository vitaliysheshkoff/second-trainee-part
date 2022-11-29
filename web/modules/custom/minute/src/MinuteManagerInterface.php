<?php

namespace Drupal\minute;

/**
 * Defines an interface for classes related with cache context.
 */
interface MinuteManagerInterface {

  /**
   * Checking even minute.
   *
   * @return bool.
   *   Checking result.
   */
  public function isEven(): bool;

}
