<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_image_import_job",
 *   label = @Translation("Pokemon: Image Import Job"),
 * )
 */
class PokemonImageImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    $payload = $job->getPayload();

    $pokemon = $this->pokemonManager->getResourceList("pokemon/{$payload['pokemon_name']}");
    $url = $pokemon['sprites']['other']['official-artwork']['front_default'];
    $entity_creation_result = $this->createMediaImage($url, 'pokemon_image', $pokemon['name'], $pokemon['id']);

    $msg = $entity_creation_result->getStatus();
    $result = $entity_creation_result->getEntity();

    return is_null($result) ? JobResult::failure($msg) : JobResult::success($msg);
  }

}
