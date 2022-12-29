<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon species as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_species",
 *   label = @Translation("Pokemon resource: species"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-species",
 *   }
 * )
 */
class PokemonSpecies extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('species_api');
  }

}
