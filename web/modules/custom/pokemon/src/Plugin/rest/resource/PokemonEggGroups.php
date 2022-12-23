<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon egg groups as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_egg_groups",
 *   label = @Translation("Pokemon resource: egg-groups"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-egg-groups",
 *   }
 * )
 */
class PokemonEggGroups extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('egg_groups_api');
  }

}
