<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

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
  public function process(Job $job): JobResult {
    return $this->taxonomyJobProcessing($job, 'pokemon-habitat', 'habitat_name', 'habitats_api');
  }

}
