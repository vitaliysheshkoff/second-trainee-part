<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon colors as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_colors",
 *   label = @Translation("Pokemon resource: colors"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-colors",
 *   }
 * )
 */
class PokemonColors extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('colors_api');
  }

}
