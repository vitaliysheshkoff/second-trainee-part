<?php

namespace Drupal\pokemon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Controller that manages pakemon node for pokemon module.
 */
class PokemonNodeController extends ControllerBase {

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
  }

}
