<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\pokemon\PokemonManager;

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

    $url = PokemonManager::IMAGES_RESOURCE_URL."/{$pokemon['id']}.png";

    $entity_creation_result = $this->createMediaImage($url, 'pokemon_image', $pokemon['name'], $pokemon['id']);

    $msg = $entity_creation_result->getStatus();
    return is_null($entity_creation_result->getEntity()) ? JobResult::failure($msg) : JobResult::success($msg);

  }

}
