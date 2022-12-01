<?php

namespace Drupal\pokemon;

/**
 * Defines an interface for pokemon module.
 */
interface PokemonManagerInterface {

  /**
   * Creates taxonomy.
   *
   * @param string $endpoint
   *   Name of endpoint.
   *
   * @param string $description
   *   Description of endpoint.
   *
   * @param array $list
   *   Endpoint resource list.
   *
   * @return ?string
   *   Either machine name of new taxonomy or null, depending on the operation performed.
   */
  public function createTaxonomy(string $endpoint, string $description, array $list): ?string;

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

  /**
   * Creates content.
   *
   * @return ?string
   *   Either machine name of new content or null, depending on the operation performed.
   */
  public function createContent(): ?string;


}
