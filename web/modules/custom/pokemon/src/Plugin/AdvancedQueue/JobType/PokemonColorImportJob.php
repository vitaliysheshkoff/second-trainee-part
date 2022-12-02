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
  public function process(Job $job) {
    $payload = $job->getPayload();

    $color = $this->pokemonManager->getResourceList("pokemon-color/{$payload['color_name']}");
    $status = $this->createTaxonomyTerm('colors_api', $color['name']);

    return ($status == SAVED_NEW || $status == SAVED_UPDATED) ?
      JobResult::success('Taxonomy was saved.')
      : (($status == NULL) ?
        JobResult::success('Taxonomy term is already exist')
        : JobResult::failure('Taxonomy creation failed'));
  }

}
