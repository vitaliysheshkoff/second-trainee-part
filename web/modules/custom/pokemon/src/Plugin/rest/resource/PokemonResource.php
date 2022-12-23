<?php

namespace Drupal\pokemon\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\taxonomy\TermInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides the base class for Pokemon API .
 *
 * @DCG
 * The plugin exposes key-value records as REST resources. In order to enable it
 * import the resource configuration into active configuration storage. An
 * example of such configuration can be located in the following file:
 * core/modules/rest/config/optional/rest.resource.entity.node.yml.
 * Alternatively you can enable it through admin interface provider by REST UI
 * module.
 * @see https://www.drupal.org/project/restui
 *
 * @DCG
 * Notice that this plugin does not provide any validation for the data.
 * Consider creating custom normalizer to validate and normalize the incoming
 * data. It can be enabled in the plugin definition as follows.
 * @code
 *   serialization_class = "Drupal\foo\MyDataStructure",
 * @endcode
 *
 * @DCG
 * For entities, it is recommended to use REST resource plugin provided by
 * Drupal core.
 * @see \Drupal\rest\Plugin\rest\resource\EntityResource
 */
abstract class PokemonResource extends ResourceBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new GetArticleResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Gets node taxonomy terms.
   *
   * @param string $vid
   *   The vocabulary id.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function getTaxTerms(string $vid) {
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $tree = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree(
      $vid,
      0,
      1,
      TRUE,
    );

    $results = [];

    foreach ($tree as $term) {
      $results[] = [
        'name' => $term->getName(),
        'id' => $term->id(),
      ];
    }

    $count = sizeof($results);
    if ($results) {
      return new ResourceResponse([
        'count' => $count,
        $vid => $results,
      ]);
    }
    else {
      $response['message'] = 'Pokemon colors were not found.';
      return new ResourceResponse($response, 400);
    }
  }

  /**
   * Gets pokemon node fields.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node.
   *
   * @return array
   *   Node fields.
   */
  protected function getFields(Node $node): array {
    $terms = [];
    $reg_fields = [];

    foreach ($node->getFields() as $key => $field) {
      if ($field->getFieldDefinition()->getType() == 'entity_reference') {
        $targetType = $field->getFieldDefinition()
          ->getItemDefinition()
          ->getSetting('target_type');
        if ($targetType == 'taxonomy_term') {
          $field_name = $field->getName();
          $new_terms = array_map(function (TermInterface $term) {
            return [
              'name' => $term->label(),
              'id' => $term->id(),
            ];
          }, $field->referencedEntities());
          $terms = array_merge($terms, [$field_name => $new_terms]);
        }
      }
      else {
        $reg_fields[] = [$key => $field];
      }
    }

    return [
      'regular_fields' => $reg_fields,
      'taxonomy_terms' => $terms,
    ];
  }

}
