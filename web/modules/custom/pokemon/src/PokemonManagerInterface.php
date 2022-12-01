<?php

namespace Drupal\pokemon;

/**
 * Defines an interface for pokemon module.
 */
interface PokemonManagerInterface {

  /**
   * Gets endpoint resource list.
   *
   * @param string $endpoint
   *   Name of endpoint.
   *
   * @param int $limit
   *   Maximum limit of records.
   *
   * @return array
   *   Endpoint resource list.
   */
  public function getResourceList(string $endpoint, int $limit): array;


}
