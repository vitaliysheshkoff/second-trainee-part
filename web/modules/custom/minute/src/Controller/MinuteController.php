<?php

namespace Drupal\minute\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
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
  protected MinuteChecker $minuteChecker;

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
  public function showCachedResult(): array {
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

    $str = $this->minuteChecker->isEven() ? 'even_title;even_entity' : 'odd_title;odd_entity';
    $data = explode(';', $str);

    $title = $config->get($data[0]);
    $entity = $data[1];

    $node = [];
    try {
      $node = $this->entityTypeManager->getStorage('node')
        ->load($config->get($entity)[0]['target_id']);
    } catch (InvalidPluginDefinitionException|PluginNotFoundException $e) {
      $error_message = $e->getMessage();
      $this->messenger()->addMessage($error_message, 'error');
    }

    return [
      '#theme' => 'minute_odd_or_even_page',
      '#title' => $title,
      '#node' => $this->entityTypeManager
        ->getViewBuilder('node')
        ->view($node, 'teaser'),
      '#cache' => [
        'tags' => ['node:' . $config->get($entity)[0]['target_id']],
        'contexts' =>
          ['minute_request_timestamp_node_and_title'],
        //['theme']
      ],
    ];
  }

}
