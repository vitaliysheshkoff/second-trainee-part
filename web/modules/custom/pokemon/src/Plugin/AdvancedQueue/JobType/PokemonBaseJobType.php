<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\pokemon\EntityCreationResult;
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
   * @param Job $job
   *   The current job.
   *
   * @param string $endpoint
   *   The name of endpoint.
   *
   * @param string $term_name
   *   The name of term.
   *
   * @param string $job_name
   *   The name of job.
   *
   * @return \Drupal\advancedqueue\JobResult Job result.
   *   Job result.
   */
  public function createTaxonomyTerm(Job $job, string $endpoint, string $term_name, string $job_name) : JobResult {
    $payload = $job->getPayload();

    $term = $this->pokemonManager->getResourceList("$endpoint/{$payload[$term_name]}");
    $entity_creation_result = $this->createTerm($job_name, $term['name']);

    $msg = $entity_creation_result->getStatus();
    return is_null($entity_creation_result->getEntity()) ? JobResult::failure($msg) : JobResult::success($msg);
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
   * @return EntityCreationResult
   *   The entity creation result.
   */
  private function createTerm(string $vid, string $term_name): EntityCreationResult {
    try {
      $term_id = $this->getTidByName($term_name, $vid);

      if ($term_id) {
        $term = $this->entityTypeManager->getStorage('taxonomy_term')
          ->load($term_id);
        return new EntityCreationResult("Taxonomy term is already exist", $term);
      }

      $term = $this->entityTypeManager
        ->getStorage('taxonomy_term')
        ->create([
          'name' => $term_name,
          'vid' => $vid,
        ]);
      $term->save();

      return new EntityCreationResult('Taxonomy term has successfully created', $term);

    } catch (\Throwable $e) {
      return new EntityCreationResult("Failure in creation new taxonomy term: {$e->getMessage()}");
    }
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
   * @return EntityCreationResult
   *   The entity creation result.
   */
  public function createNode(array $fields, array $tax_fields, string $title, string $type): EntityCreationResult {
    try {
      // maybe get id_field and check the node by them(not by title)
      $query = $this->entityTypeManager->getStorage('node')->getQuery();
      $query->condition('title', $title);
      $node_ids = $query->execute();

      if (!empty($node_ids)) {
        $node = $this->entityTypeManager->getStorage('node')
          ->load(reset($node_ids));
        return new EntityCreationResult("Node is already exist", $node);
      }

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
            $this->createTerm($tax_field['vid'], $term);
            $term_id = $this->getTidByName($term, $tax_field['vid']);
          }
          $terms[] = ['target_id' => $term_id];
        }
        $node->set($tax_field['field_name'], $terms);
      }
      $node->save();
      return new EntityCreationResult('Node has successfully created', $node);
    } catch (\Throwable $e) {
      return new EntityCreationResult("Failure in creation new node: {$e->getMessage()}");
    }
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
