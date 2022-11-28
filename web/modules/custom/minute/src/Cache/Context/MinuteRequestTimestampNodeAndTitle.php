<?php

namespace Drupal\minute\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\minute\MinuteChecker;

/**
 * Cache context ID: 'minute_request_timestamp_node_and_title'.
 */
class MinuteRequestTimestampNodeAndTitle implements CacheContextInterface {

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
    return t('Minute request timestamp node and title');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext(): string {
    return $this->minuteChecker->isEven() ? 'even_title;even_entity' : 'odd_title;odd_entity';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
