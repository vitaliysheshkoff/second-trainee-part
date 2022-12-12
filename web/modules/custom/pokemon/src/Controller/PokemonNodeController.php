<?php

namespace Drupal\pokemon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\TermInterface;

/**
 * Class PokemonNodeController.
 */
class PokemonNodeController extends ControllerBase {
  /**
   * {@inheritdoc}
   */
  public function render(Node $node) {
      return [
        '#theme' => 'pokemon_node',
        '#node' => $node,
        '#attached' => ['library' => ['pokemon/node-style']],
      ];
    }

}
