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
  public function process(Job $job) {
    $payload = $job->getPayload();

    $shape = $this->pokemonManager->getResourceList("pokemon-shape/{$payload['shape_name']}");
    $status = $this->createTaxonomyTerm('shapes_api', $shape['name']);

    return ($status == SAVED_NEW || $status == SAVED_UPDATED) ?
      JobResult::success('Taxonomy was saved.')
      : (($status == NULL) ?
        JobResult::success('Taxonomy term is already exist')
        : JobResult::failure('Taxonomy creation failed'));
  }

}
