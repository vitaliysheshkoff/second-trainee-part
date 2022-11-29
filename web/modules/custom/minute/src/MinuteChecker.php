<?php

namespace Drupal\minute;

use Drupal\Component\Datetime\Time;

/**
 *  Minute checker service for minute module.
 */
class MinuteChecker implements MinuteManagerInterface {

  /**
   * A Time object.
   *
   * @var \Drupal\Component\Datetime\Time
   */
  protected $time;

  /**
   * MinuteChecker constructor.
   *
   * @param \Drupal\Component\Datetime\Time $time
   *   A Time object.
   */
  public function __construct(Time $time) {
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public function isEven(): bool {
    return  intval($this->time->getCurrentTime() / 60) % 2 == 0;
  }




}
