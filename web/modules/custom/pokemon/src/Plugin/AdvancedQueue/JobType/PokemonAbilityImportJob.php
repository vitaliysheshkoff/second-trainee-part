<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_ability_import_job",
 *   label = @Translation("Pokemon: Ability Import Job"),
 * )
 */
class PokemonAbilityImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    return $this->createTaxonomyTerm($job, 'ability', 'ability_name', 'abilities_api');
  }

}
