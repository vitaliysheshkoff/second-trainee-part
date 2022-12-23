<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon abilities as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_abilities",
 *   label = @Translation("Pokemon resource: abilities"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-abilities",
 *   }
 * )
 */
class PokemonAbilities extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('abilities_api');
  }

}
