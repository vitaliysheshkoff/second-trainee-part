<?php

namespace Drupal\minute\Cache\Context;

use Drupal\Component\Datetime\Time;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\minute\MinuteChecker;

/**
 * Cache context ID: 'minute_request_timestamp'.
 */
class MinuteRequestTimestamp implements CacheContextInterface{

  /**
   * @var \Drupal\minute\MinuteChecker
   */
  protected MinuteChecker $minuteChecker;

  /**
   * {@inheritdoc}
   */
  public function __construct(MinuteChecker $minute_checker) {
    $this->minuteChecker = $minute_checker;
  }

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return t('Minute request timestamp');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext(): string {
    return $this->minuteChecker->isEven() ? 'Even Minute' : 'Odd Minute';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
