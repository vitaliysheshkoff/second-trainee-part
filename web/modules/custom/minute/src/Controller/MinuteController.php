<?php

namespace Drupal\minute\Controller;

use \Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\minute\Form\MinuteTitleAndNodeConfigForm;
use Drupal\minute\MinuteChecker;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for minute module.
 */
class MinuteController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\minute\MinuteChecker
   */
  protected $minuteChecker;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(MinuteChecker $minute_checker, ConfigFactoryInterface $config_factory, EntityTypeManager $entity_type_manager) {
    $this->minuteChecker = $minute_checker;
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('minute.minute_checker'),
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Provides showing cashed result.
   *
   * @return array
   *   Cached result.
   */
  public function showTextPage(): array {
    return [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->minuteChecker->isEven() ? 'Even Minute' : 'Odd Minute',
      '#attributes' => [
        'class' => ['time-example'],
      ],
      '#cache' => [
        'contexts' =>
          ['minute_request_timestamp'],
      ],
    ];
  }

  /**
   * Show odd or even theme.
   *
   * @return array
   *   odd or even theme.
   */
  public function showPageWithTitleAndNode(): array {
    $config = $this->configFactory->getEditable(MinuteTitleAndNodeConfigForm::SETTINGS);

    $title = 'odd_title';
    $entity = 'odd_entity';

    if ($this->minuteChecker->isEven()) {
      $title = 'even_title';
      $entity = 'even_entity';
    }

    $title = $config->get($title);

    $node = $this->entityTypeManager->getStorage('node')
      ->load($config->get($entity)[0]['target_id']);

    if (!is_null($node)) {
      $node = $this->entityTypeManager
        ->getViewBuilder('node')
        ->view($node, 'teaser');
    }

    return [
      '#theme' => 'minute_odd_or_even_page',
      '#title' => $title,
      '#node' => $node,
      '#cache' => [
        'tags' => ['node:' . $config->get($entity)[0]['target_id']],
        'contexts' =>
          ['minute_request_timestamp'],
      ],
    ];
  }

}
