<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Annotation\AdvancedQueueJobType;
use Drupal\advancedqueue\Job;

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
    /** @var \Drupal\pokemon\PokemonManager $pokemon_manager */
    $pokemon_manager = \Drupal::service('pokemon.pokemon_manager');

    $payload = $job->getPayload();
    // Как только мы получили пэйлоад мы должны вызвать менеджера покемонов
    // В этом менеджере мы должны вызвать метод который по определенному эндпоинту вернет нам результат.
    // Результат мы запихнем в метод создания таксономии
    $pokemon_manager->createTaxonomy($payload['endpoint'], $payload['description'], $payload['list']);
    // Метод по созданию таксономии нам тоже должен что-то возвращать
    // И на основании этого результата мы уже будет возвращать
    // return JobResult(и сюда уже нужно передавать необходимые параметры посмотри уже этот метод сам);
  }

}
