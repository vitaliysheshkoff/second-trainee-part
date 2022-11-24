<?php

namespace Drupal\minute\Controller;

use Drupal\Component\Datetime\Time;
use Drupal\Core\Controller\ControllerBase;
use Drupal\minute\MinuteChecker;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for minute module.
 */
class MinuteController extends ControllerBase {

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
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('minute.minute_checker'),
    );
  }

  /**
   * Provides showing cashed result.
   *
   * @return array
   *   Cached result.
   */
  public function showCachedResult(): array {
    return [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->minuteChecker->isEven() ? 'Even Minute' : 'Odd Minute',
      '#attributes' => [
        'class' => ['time-example'],
      ],
      '#cache' => [
        'contexts' => [
          'minute_request_timestamp',
        ],
      ],
    ];
  }

}
