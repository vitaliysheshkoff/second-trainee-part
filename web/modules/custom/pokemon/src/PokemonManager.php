<?php

namespace Drupal\pokemon;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Vocabulary;
use PokePHP\PokeApi;

/**
 *  Pokemon manager service for creation content.
 */
class PokemonManager implements PokemonManagerInterface {

  /**
   * Maximum limit of records.
   *
   * @var int
   */
  const MAX_LIMIT = 1323;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * PokemonManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   An entity type manager.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceList(string $endpoint, int $limit = self::MAX_LIMIT): array {
    $api = new PokeApi;
    $resource_list_json = $api->resourceList($endpoint, $limit);
    return json_decode($resource_list_json, TRUE);
  }

}
