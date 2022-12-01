<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemons_import_job",
 *   label = @Translation("Pokemons Import Job"),
 *   max_retries = 1,
 *   retry_delay = 79200,
 * )
 */
class PokemonsImportJob extends JobTypeBase {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job) {
    /** @var \Drupal\pokemon\PokemonManager $pokemon_manager */
    $pokemon_manager = \Drupal::service('pokemon.pokemon_manager');

    $payload = $job->getPayload();
    $pokemon_manager->createTaxonomy($payload['endpoint'], $payload['description'], $payload['list']);
  }

}
