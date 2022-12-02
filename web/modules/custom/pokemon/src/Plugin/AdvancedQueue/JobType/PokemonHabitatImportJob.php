<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_habitat_import_job",
 *   label = @Translation("Pokemon: Habitat Import Job"),
 * )
 */
class PokemonHabitatImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job) {
    $payload = $job->getPayload();

    $habitat = $this->pokemonManager->getResourceList("pokemon-habitat/{$payload['habitat_name']}");
    $status = $this->createTaxonomyTerm('habitats_api', $habitat['name']);

    return ($status == SAVED_NEW || $status == SAVED_UPDATED) ?
      JobResult::success('Taxonomy was saved.')
      : (($status == NULL) ?
        JobResult::success('Taxonomy term is already exist')
        : JobResult::failure('Taxonomy creation failed'));
  }

}
