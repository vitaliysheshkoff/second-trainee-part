<?php

namespace Drupal\pokemon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Psr\Container\ContainerInterface;

/**
 * Controller that manages pakemon node for pokemon module.
 */
class PokemonNodeController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * PokemonNodeController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render(Node $node) {
    if ($node->bundle() === 'pokemon') {
      return [
        '#theme' => 'pokemon_node',
        '#node' => $node,
        '#attached' => ['library' => ['pokemon/node-style']],
        '#cache' => [
          'tags' => ['node_list'],
        ],
      ];
    }
    else {
      return $this->entityTypeManager
        ->getViewBuilder('node')
        ->view($node, 'default');
    }
  }

}
