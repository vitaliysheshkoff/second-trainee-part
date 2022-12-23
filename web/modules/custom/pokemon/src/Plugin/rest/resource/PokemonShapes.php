<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon shapes as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_shapes",
 *   label = @Translation("Pokemon resource: shapes"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-shapes",
 *   }
 * )
 */
class PokemonShapes extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('shapes_api');
  }

}
