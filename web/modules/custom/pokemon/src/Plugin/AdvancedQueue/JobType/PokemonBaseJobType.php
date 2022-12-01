<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\pokemon\PokemonManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the base class for Pokemon Import job types.
 */
abstract class PokemonBaseJobType extends JobTypeBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Helper service to common functionality with Pokemon.
   *
   * @var \Drupal\pokemon\PokemonManager
   */
  protected $pokemonManager;

  /**
   * Constructs a new SyncDbProductImport object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity query factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, PokemonManager $pokemon_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->pokemonManager = $pokemon_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('pokemon.pokemon_manager')
    );
  }

  /**
   * Creates taxonomy term.
   *
   * @param string $vid
   *   Vocabulary machine name.
   *
   * @param string $term_name
   *   Taxonomy term name.
   *
   * @return int
   *   Either SAVED_NEW or SAVED_UPDATED, depending on the operation performed.
   */
  public function createTaxonomyTerm(string $vid, string $term_name): int {
    $term = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->create([
        'name' => $term_name,
        'vid' => $vid,
      ]);

    return $term->save();
  }

}
