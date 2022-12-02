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
  public function process(Job $job) {
    $payload = $job->getPayload();

    $type = $this->pokemonManager->getResourceList("type/{$payload['type_name']}");
    $status = $this->createTaxonomyTerm('types_api', $type['name']);

    return ($status == SAVED_NEW || $status == SAVED_UPDATED) ?
      JobResult::success('Taxonomy was saved.')
      : (($status == NULL) ?
        JobResult::success('Taxonomy term is already exist')
        : JobResult::failure('Taxonomy creation failed'));
  }

}
