<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_type_import_job",
 *   label = @Translation("Pokemon: Type Import Job"),
 * )
 */
class PokemonTypeImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    return $this->taxonomyJobProcessing($job, 'type', 'type_name', 'types_api');
  }

}
