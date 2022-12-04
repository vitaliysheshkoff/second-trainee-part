<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_node_import_job",
 *   label = @Translation("Pokemon: Node Import Job"),
 * )
 */
class PokemonNodeImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job) {
    $payload = $job->getPayload();

    $pokemon = $this->pokemonManager->getResourceList("pokemon/{$payload['pokemon_name']}");

    $species = $pokemon['species']['name'];
    $abilities = $this->getTaxField($pokemon['abilities'], 'ability');
    $forms = $this->getTaxField($pokemon['forms']);
    $stats = $this->getTaxField($pokemon['stats'], 'stat');
    $types = $this->getTaxField($pokemon['types'], 'type');

    $pokemon_species = $this->pokemonManager->getResourceList("pokemon-species/{$species}");
    $egg_group[] = $this->getTaxField($pokemon_species['egg_groups']);

    $fields = [
      [
        'field_name' => 'field_name',
        'value' => $pokemon['name'],
      ],
      [
        'field_name' => 'field_id',
        'value' => $pokemon['id'],
      ],
      [
        'field_name' => 'field_height',
        'value' => $pokemon['height'],
      ],
    ];

    $tax_fields = [
      [
        'field_name' => 'field_ability',
        'vid' => 'ability_api',
        'terms' => $abilities,
      ],
      [
        'field_name' => 'field_form',
        'vid' => 'forms_api',
        'terms' => $forms,
      ],

      [
        'field_name' => 'field_type',
        'vid' => 'types_api',
        'terms' => $types,
      ],
      [
        'field_name' => 'field_stat',
        'vid' => 'stats_api',
        'terms' => $stats,
      ],
      [
        'field_name' => 'field_specie',
        'vid' => 'species_api',
        'terms' => [
          $species,
        ],
      ],
      [
        'field_name' => 'field_color',
        'vid' => 'colors_api',
        'terms' => [
          $pokemon_species['color']['name'],
        ],
      ],
      [
        'field_name' => 'field_habitat',
        'vid' => 'habitats_api',
        'terms' => [
          $pokemon_species['habitat']['name'],
        ],
      ],
      [
        'field_name' => 'field_shape',
        'vid' => 'shapes_api',
        'terms' => [
          $pokemon_species['shape']['name'],
        ],
      ],
      [
        'field_name' => 'field_egg_group',
        'vid' => 'egg_groups_api',
        'terms' => $egg_group,
      ],
    ];

    $status = $this->createNode($fields, $tax_fields, $pokemon['name'], 'pokemon');

    return ($status == SAVED_NEW || $status == SAVED_UPDATED) ?
      JobResult::success('Node was saved.')
      : (($status == NULL) ?
        JobResult::success('Node is already exist')
        : JobResult::failure('Node creation failed'));
  }

  /**
   * Gets taxonomy field.
   *
   * @param array $tax_field_terms
   *  Taxonomy field terms.
   *
   * @param string|null $name
   *  An optional name where term place.
   *
   * @return array
   *  Terms of taxonomy field.
   */
  public function getTaxField(array $tax_field_terms, string $name = NULL): array {
    $terms = [];
    foreach ($tax_field_terms as $term) {
      $terms[] = is_null($name) ? $term['name'] : $term[$name]['name'];
    }
    return $terms;
  }

}
