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
  public function process(Job $job) {
    $payload = $job->getPayload();

    $color = $this->pokemonManager->getResourceList("stat/{$payload['stat_name']}");
    $status = $this->createTaxonomyTerm('stats_api', $color['name']);

    return ($status == SAVED_NEW || $status == SAVED_UPDATED) ?
      JobResult::success('Taxonomy was saved.')
      : (($status == NULL) ?
        JobResult::success('Taxonomy term is already exist')
        : JobResult::failure('Taxonomy creation failed'));
  }

}
