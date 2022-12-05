<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_egg_group_import_job",
 *   label = @Translation("Pokemon: Egg Group Import Job"),
 * )
 */
class PokemonEggGroupImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    return $this->createTaxonomyTerm($job, 'egg-group', 'egg_group_name', 'egg_groups_api');
  }

}
