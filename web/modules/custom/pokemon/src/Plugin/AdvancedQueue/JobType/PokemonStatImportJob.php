<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_stat_import_job",
 *   label = @Translation("Pokemon: Stat Import Job"),
 * )
 */
class PokemonStatImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    return $this->createTaxonomyTerm($job, 'stat', 'stat_name', 'stats_api');
  }

}
