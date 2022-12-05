<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_shape_import_job",
 *   label = @Translation("Pokemon: Shape Import Job"),
 * )
 */
class PokemonShapeImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    return $this->createTaxonomyTerm($job, 'pokemon-shape', 'shape_name', 'shapes_api');
  }

}
