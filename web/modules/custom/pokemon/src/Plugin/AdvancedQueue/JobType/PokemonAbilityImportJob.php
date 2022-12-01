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
  public function process(Job $job) {
    $payload = $job->getPayload();

    $ability = $this->pokemonManager->getResourceList("ability/{$payload['ability_name']}",327 );
    $status = $this->createTaxonomyTerm('ability_api', $ability['name']);

    return ($status == SAVED_NEW || $status == SAVED_UPDATED) ? JobResult::success('Node was saved.') : JobResult::failure('Node creation failed');
  }

}
