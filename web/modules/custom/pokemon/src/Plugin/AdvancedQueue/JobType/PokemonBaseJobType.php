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
   * @return ?int
   *   Either SAVED_NEW or SAVED_UPDATED, or NULL depending on the operation
   *   performed.
   */
  public function createTaxonomyTerm(string $vid, string $term_name): ?int {
    $term_id = $this->getTidByName($term_name, $vid);

    if ($term_id) {
      return NULL;
    }

    $term = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->create([
        'name' => $term_name,
        'vid' => $vid,
      ]);

    return $term->save();
  }

  /**
   * Creates node with fields.
   *
   * @param array $fields
   *   List of regular fields.
   *
   * @param array $tax_fields
   *   List of taxonomy fields.
   *
   * @param string $title
   *   Node title.
   *
   * @param string $type
   *   Node type.
   *
   * @return int
   *   Either SAVED_NEW or SAVED_UPDATED, depending on the operation performed.
   */
  public function createNode(array $fields, array $tax_fields, string $title, string $type): int {
    $node = $this->entityTypeManager
      ->getStorage('node')
      ->create([
        'type' => $type,
        'title' => $title,
      ]);
    $node->save();

    // Set regular fields.
    foreach ($fields as $field) {
      $node->set($field['field_name'], $field['value']);
    }

    // Set taxonomy fields.
    foreach ($tax_fields as $tax_field) {
      $terms = [];
      foreach ($tax_field['terms'] as $term) {
        $term_id = $this->getTidByName($term, $tax_field['vid']);
        if (!$term_id) {
          // Create new taxonomy term.
          $this->createTaxonomyTerm($tax_field['vid'], $term);
          $term_id = $this->getTidByName($term, $tax_field['vid']);
        }
        $terms[] = ['target_id' => $term_id];
      }
      $node->set($tax_field['field_name'], $terms);
    }
    return $node->save();
  }

  /**
   * Finds term by name and vid.
   *
   * @param null $name
   *  Term name.
   * @param null $vid
   *  Term vid.
   *
   * @return int
   *  Term id or 0 if none.
   */
  protected function getTidByName($name = NULL, $vid = NULL): int {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term->id() : 0;
  }

}
