<?php

namespace Drupal\pokemon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\TermInterface;

/**
 * Class PokemonNodeController.
 */
class PokemonNodeController extends ControllerBase {

  const POKEMONS_FIELDS = [
    'field_ability',
    'field_color',
    'field_egg_group',
    'field_form',
    'field_habitat',
    'field_height',
    'field_id',
    'field_name',
    'field_pokemon_image',
    'field_shape',
    'field_specie',
    'field_stat',
    'field_type',
    'af'
  ];

  /**
   * {@inheritdoc}
   */
  public function render(Node $node) {
    if ($node->bundle() === 'pokemon') {
      return [
        '#theme' => 'pokemon_node',
        '#node' => $node,
        '#attached' => ['library' => ['pokemon/node-style']],
      ];
    }
  }

}
