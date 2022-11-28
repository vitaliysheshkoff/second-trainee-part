<?php

namespace Drupal\minute\Plugin\Block;

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
   * @var \Drupal\minute\MinuteChecker
   */
  protected MinuteChecker $minuteChecker;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MinuteChecker $minute_checker) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
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
      $container->get('minute.minute_checker'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $result = $this->minuteChecker->isEven() ? 'Even Minute' : 'Odd Minute';
    return [
      '#markup' => $result,
      '#cache' => [
        'contexts' => $this->getCacheContexts(),
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
