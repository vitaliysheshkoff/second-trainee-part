<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

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
    $egg_group = $this->getTaxField($pokemon_species['egg_groups']);

    // [0] => field name, [1] => field id.
    $fields_properties = [
      ['field_name', $pokemon['name']],
      ['field_id', $pokemon['id']],
      ['field_height', $pokemon['height']],
    ];

    // [0] => field name, [1] => vid, [2] => terms.
    $tax_field_properties = [
      ['field_ability', 'abilities_api', $abilities],
      ['field_form', 'forms_api', $forms],
      ['field_type', 'types_api', $types],
      ['field_stat', 'stats_api', $stats],
      ['field_specie', 'species_api', [$species]],
      ['field_color', 'colors_api', [$pokemon_species['color']['name']]],
      ['field_habitat', 'habitats_api', [$pokemon_species['habitat']['name']]],
      ['field_shape', 'shapes_api', [$pokemon_species['shape']['name']]],
      ['field_egg_group', 'egg_groups_api', $egg_group],
    ];

    $fields = [];
    foreach ($fields_properties as $field) {
      $fields[] = $this->setField($field[0], $field[1]);
    }

    $tax_fields = [];
    foreach ($tax_field_properties as $tax_field) {
      $tax_fields[] = $this->setTaxonomyField($tax_field[0], $tax_field[1], $tax_field[2]);
    }

    $img_field = $this->setImageField(
      'pokemon_image',
      'field_pokemon_image',
      $pokemon['sprites']['other']['official-artwork']['front_default'],
      $pokemon['id'], $pokemon['name'],
    );

    $entity_creation_result = $this->createNode($fields, $tax_fields, $img_field, $pokemon['name'], 'pokemon');

    $msg = $entity_creation_result->getStatus();
    $result = $entity_creation_result->getEntity();
    return is_null($result) ? JobResult::failure($msg) : JobResult::success($msg);
  }

  /**
   * @param string $field_name
   *   The name of the field.
   *
   * @param string $vid
   *   The taxonomy machine name.
   *
   * @param array $terms
   *   The array of items.
   *
   * @return array
   *   The taxonomy field.
   */
  protected function setTaxonomyField(string $field_name, string $vid, array $terms): array {
    return [
      'field_name' => $field_name,
      'vid' => $vid,
      'terms' => $terms,
    ];
  }

  /**
   * Sets default field.
   *
   * @param string $field_name
   *   The name of the field.
   *
   * @param string $value
   *   The value of the field.
   *
   * @return array
   *   The field.
   */
  protected function setField(string $field_name, string $value): array {
    return [
      'field_name' => $field_name,
      'value' => $value,
    ];
  }

  /**
   * Sets image field.
   *
   * @param string $bundle
   *   The term bundle.
   *
   * @param string $field_name
   *   The name of the field.
   *
   * @param string $img_url
   *   The img url.
   *
   * @param int $pokemon_id
   *   The id of pokemon.
   *
   * @param string $pokemon_name
   *   The name of pokemon.
   *
   * @return array
   *   The list of image fields.
   */
  protected function setImageField(string $bundle, string $field_name, string $img_url, int $pokemon_id, string $pokemon_name): array {
    return [
      'url' => $img_url,
      'field_name' => $field_name,
      'properties' => [
        'field_pokemon_id' => $pokemon_id,
        'field_pokemon_name' => $pokemon_name,
        'bundle' => $bundle,
      ],
    ];
  }

  /**
   * Gets taxonomy field.
   *
   * @param array $tax_field_terms
   *   Taxonomy field terms.
   *
   * @param string|null $name
   *   An optional name where term place.
   *
   * @return array
   *   Terms of taxonomy field.
   */
  protected function getTaxField(array $tax_field_terms, string $name = NULL): array {
    $terms = [];
    foreach ($tax_field_terms as $term) {
      $terms[] = is_null($name) ? $term['name'] : $term[$name]['name'];
    }
    return $terms;
  }

}
