<?php

namespace Drupal\pokemon\Plugin\rest\resource;

/**
 * Represents pokemon forms as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_forms",
 *   label = @Translation("Pokemon resource: forms"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-forms",
 *   }
 * )
 */
class PokemonForms extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    return $this->getTaxTerms('forms_api');
  }

}
