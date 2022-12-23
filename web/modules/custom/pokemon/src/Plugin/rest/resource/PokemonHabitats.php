<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon habitats as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_habitats",
 *   label = @Translation("Pokemon resource: habitats"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-habitats",
 *   }
 * )
 */
class PokemonHabitats extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('habitats_api');
  }

}
