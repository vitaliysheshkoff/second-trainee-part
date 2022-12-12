<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;

/**
 * @AdvancedQueueJobType(
 *   id = "pokemon_color_import_job",
 *   label = @Translation("Pokemon: Color Import Job"),
 * )
 */
class PokemonColorImportJob extends PokemonBaseJobType {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job): JobResult {
    return $this->taxonomyJobProcessing($job, 'pokemon-color', 'color_name', 'colors_api');
  }

}
