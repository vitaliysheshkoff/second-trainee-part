<?php

namespace Drupal\minute\Plugin\Block;

use Drupal\Component\Datetime\Time;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\minute\MinuteChecker;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with user table.
 *
 * @Block(
 *   id = "minute_old_or_even_block",
 *   admin_label = @Translation("Old or Even Block"),
 * )
 */
class OddOrEvenBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Component\Datetime\Time
   */
  protected Time $time;

  /**
   * @var \Drupal\minute\MinuteChecker
   */
  protected MinuteChecker $minuteChecker;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Time $time, MinuteChecker $minute_checker) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->time = $time;
    $this->minuteChecker = $minute_checker;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('datetime.time'),
      $container->get('minute.minute_checker'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $minutes_amount = intval($this->time->getCurrentTime() / 60);
    $result =  $this->minuteChecker->isEven($minutes_amount) ? 'Even Minute' : 'Odd Minute';

    return [
      '#markup' => $result,
      '#cache' => [
        'contexts' => /*$this->getCacheContexts()*/ ['minute_request_timestamp'],
      ],
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(
      parent::getCacheContexts(),
      ['minute_request_timestamp']
    );
  }

}
