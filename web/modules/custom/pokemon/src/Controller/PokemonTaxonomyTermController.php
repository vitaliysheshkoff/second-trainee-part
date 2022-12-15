<?php

namespace Drupal\pokemon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\TermInterface;

/**
 * Provides controller for taxonomy terms.
 */
class PokemonTaxonomyTermController extends ControllerBase {

  /**
   * List of vocabularies.
   *
   * @var array
   */
  const VOCABULARIES = [
    'abilities_api',
    'colors_api',
    'egg_groups_api',
    'forms_api',
    'habitats_api',
    'shapes_api',
    'species_api',
    'stats_api',
    'types_api',
  ];

  /**
   * {@inheritdoc}
   */
  public function render(TermInterface $taxonomy_term) {
    if (in_array($taxonomy_term->bundle(), self::VOCABULARIES)) {
      return [
        '#theme' => 'pokemon_tax_term',
        '#taxonomy_term' => $taxonomy_term,
        '#attached' => ['library' => ['pokemon/tax-style']],
        '#cache' => [
          'tags' => ['taxonomy_term_list'],
        ],
      ];
    }
  }

}
