<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon stats as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_stats",
 *   label = @Translation("Pokemon resource: stats"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-stats",
 *   }
 * )
 */
class PokemonStats extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('stats_api');
  }

}
