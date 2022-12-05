<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_species_import_job",
 *   label = @Translation("Pokemon: Species Import Job"),
 * )
 */
class PokemonSpeciesImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    return $this->createTaxonomyTerm($job, 'pokemon-species', 'species_name', 'species_api');
  }

}
