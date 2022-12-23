<?php

namespace Drupal\pokemon\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystem;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\media\Entity\Media;
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
   * File system service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

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
   * @param \Drupal\pokemon\PokemonManager $pokemon_manager
   *   Helper service to common functionality with Pokemon.
   * @param \Drupal\Core\File\FileSystem $file_system
   *   File system service.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              EntityTypeManagerInterface $entity_type_manager,
                              PokemonManager $pokemon_manager,
                              FileSystem $file_system) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->pokemonManager = $pokemon_manager;
    $this->fileSystem = $file_system;

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
      $container->get('pokemon.pokemon_manager'),
      $container->get('file_system'),
    );
  }

  /**
   * Creates media image.
   *
   * @param string $url
   *   The image ulr.
   * @param string $bundle_name
   *   The name of the bundle.
   * @param string $pokemon_name
   *   The name of pokemon.
   * @param string $pokemon_id
   *   The id of pokemon.
   * @param int $uid
   *   The user id.
   * @param string $dir
   *   Temporary dir.
   *
   * @return \Drupal\pokemon\EntityCreationResult
   *   Entity creation result result.
   */
  public function createMediaImage(string $url,
                                   string $bundle_name,
                                   string $pokemon_name,
                                   string $pokemon_id,
                                   int $uid = 1,
                                   string $dir = 'public://media'): EntityCreationResult {
    $img_field = 'field_media_image_1';
    $id_field = 'field_pokemon_id';
    $name_field = 'field_pokemon_name';

    try {
      $media_id = $this->getTidByName('media', [
        $id_field => $pokemon_id,
        $name_field => $pokemon_name,
        'bundle' => 'pokemon_image',
      ]);

      if ($media_id) {
        $media = $this->entityTypeManager->getStorage('media')
          ->load($media_id);

        return new EntityCreationResult("Media is already exist", $media);
      }

      if ($this->fileSystem->prepareDirectory($dir, FileSystemInterface::CREATE_DIRECTORY)) {
        $file = system_retrieve_file(trim($url), $dir, TRUE);
      }

      if (!isset($file)) {
        return new EntityCreationResult("Can't gets the image");
      }

      $media = Media::create([
        'bundle' => $bundle_name,
        'uid' => $uid,
        'name' => $pokemon_name,
        $img_field => [
          'target_id' => $file->id(),
          'alt' => $pokemon_name,
        ],
        $id_field => [
          'value' => $pokemon_id,
        ],
        $name_field => [
          'value' => $pokemon_name,
        ],
      ]);
      $media->save();

      return new EntityCreationResult('Media image has successfully created', $media);

    }
    catch (\Throwable $e) {
      return new EntityCreationResult("Failure in creation new media image: {$e->getMessage()}");
    }
  }

  /**
   * Creates taxonomy term.
   *
   * @param \Drupal\advancedqueue\Job $job
   *   The current job.
   * @param string $endpoint
   *   The name of endpoint.
   * @param string $term_name
   *   The name of term.
   * @param string $job_name
   *   The name of job.
   *
   * @return \Drupal\advancedqueue\JobResult
   *   Job result.
   */
  protected function taxonomyJobProcessing(Job $job, string $endpoint, string $term_name, string $job_name): JobResult {
    $payload = $job->getPayload();

    $term = $this->pokemonManager->getResourceList("$endpoint/{$payload[$term_name]}");
    $entity_creation_result = $this->createTerm($job_name, $term['name']);

    $msg = $entity_creation_result->getStatus();
    $result = $entity_creation_result->getEntity();

    return is_null($result) ? JobResult::failure($msg) : JobResult::success($msg);
  }

  /**
   * Creates taxonomy term.
   *
   * @param string $vid
   *   Vocabulary machine name.
   * @param string $term_name
   *   Taxonomy term name.
   *
   * @return \Drupal\pokemon\EntityCreationResult
   *   The entity creation result.
   */
  protected function createTerm(string $vid, string $term_name): EntityCreationResult {
    try {
      $term_id = $this->getTidByName('taxonomy_term', [
        'name' => $term_name,
        'vid' => $vid,
      ]);

      if ($term_id) {
        $term = $this->entityTypeManager->getStorage('taxonomy_term')
          ->load($term_id);
        return new EntityCreationResult("Taxonomy term is already exist", $term);
      }

      $term = $this->entityTypeManager
        ->getStorage('taxonomy_term')
        ->create([
          'uid' => 1,
          'name' => $term_name,
          'vid' => $vid,
        ]);
      $term->save();

      return new EntityCreationResult('Taxonomy term has successfully created', $term);

    }
    catch (\Throwable $e) {
      return new EntityCreationResult("Failure in creation new taxonomy term: {$e->getMessage()}");
    }
  }

  /**
   * Creates node with fields.
   *
   * @param array $fields
   *   List of regular fields.
   * @param array $tax_fields
   *   List of taxonomy fields.
   * @param array $img_field
   *   The image.
   * @param string $title
   *   Node title.
   * @param string $type
   *   Node type.
   *
   * @return \Drupal\pokemon\EntityCreationResult
   *   The entity creation result.
   */
  public function createNode(array $fields, array $tax_fields, array $img_field, string $title, string $type): EntityCreationResult {
    try {
      // Maybe get id_field and check the node by them(not by title)
      $query = $this->entityTypeManager->getStorage('node')->getQuery();
      $query->condition('title', $title);
      $node_ids = $query->accessCheck(FALSE)->execute();

      if (!empty($node_ids)) {
        $node = $this->entityTypeManager->getStorage('node')
          ->load(reset($node_ids));
        return new EntityCreationResult("Node is already exist", $node);
      }

      $node = $this->entityTypeManager
        ->getStorage('node')
        ->create([
          'uid' => 1,
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
          $term_id = $this->getTidByName('taxonomy_term', [
            'name' => $term,
            'vid' => $tax_field['vid'],
          ]);
          if (!$term_id) {
            // Create new taxonomy term.
            $this->createTerm($tax_field['vid'], $term);
            $term_id = $this->getTidByName('taxonomy_term', [
              'name' => $term,
              'vid' => $tax_field['vid'],
            ]);
          }
          $terms[] = ['target_id' => $term_id];
        }
        $node->set($tax_field['field_name'], $terms);
      }

      // Set Img field.
      $media_id = $this->getTidByName('media', $img_field['properties']);
      if (!$media_id) {
        // Create new media image.
        $this->createMediaImage(
          $img_field['url'],
          $img_field['properties']['bundle'],
          $img_field['properties']['field_pokemon_name'],
          $img_field['properties']['field_pokemon_id'],
        );
        $media_id = $this->getTidByName('media', $img_field['properties']);
      }

      $node->set($img_field['field_name'], ['target_id' => $media_id]);
      $node->save();

      return new EntityCreationResult('Node has successfully created', $node);

    }
    catch (\Throwable $e) {
      return new EntityCreationResult("Failure in creation new node: {$e->getMessage()}");
    }
  }

  /**
   * Finds entity id by properties.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param array $properties
   *   The entity properties.
   *
   * @return int
   *   Term id or 0 if none.
   */
  protected function getTidByName(string $entity_type_id, array $properties): int {
    $entity = $this->entityTypeManager->getStorage($entity_type_id)
      ->loadByProperties($properties);
    $entity = reset($entity);

    return !empty($entity) ? $entity->id() : 0;
  }

}
