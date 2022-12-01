<?php

namespace Drupal\pokemon;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Vocabulary;
use PokePHP\PokeApi;

/**
 *  Pokemon manager service for creation content.
 */
class PokemonManager implements PokemonManagerInterface {

  /**
   * Maximum limit of records.
   *
   * @var int
   */
  const MAX_LIMIT = 1323;

  /**
   * Taxonomy endpoints.
   *
   * @var array
   */
  private static array $taxonomyEndpoints = [
    'ability' => 'Abilities provide passive effects for Pokémon in battle or in the overworld. Pokémon have multiple possible abilities but can have only one ability at a time.',
    'pokemon-color' => "Colors used for sorting Pokémon in a Pokédex. The color listed in the Pokédex is usually the color most apparent or covering each Pokémon's body. No orange category exists; Pokémon that are primarily orange are listed as red or brown.",
    'pokemon-habitat' => "Habitats are generally different terrain Pokémon can be found in but can also be areas designated for rare or legendary Pokémon.",
    'pokemon-shape' => "Shapes used for sorting Pokémon in a Pokédex.",
    'pokemon-species' => "A Pokémon Species forms the basis for at least one Pokémon. Attributes of a Pokémon species are shared across all varieties of Pokémon within the species. A good example is Wormadam; Wormadam is the species which can be found in three different varieties, Wormadam-Trash, Wormadam-Sandy and Wormadam-Plant.",
    'pokemon-form' => "Some Pokémon may appear in one of multiple, visually different forms. These differences are purely cosmetic. For variations within a Pokémon species, which do differ in more than just visuals, the 'Pokémon' entity is used to represent such a variety.",
    'type' => "Types are properties for Pokémon and their moves. Each type has three properties: which types of Pokémon it is super effective against, which types of Pokémon it is not very effective against, and which types of Pokémon it is completely ineffective against.",
    'stat' => "Stats determine certain aspects of battles. Each Pokémon has a value for each stat which grows as they gain levels and can be altered momentarily by effects in battles.",
    'egg-group' => "Egg Groups are categories which determine which Pokémon are able to interbreed. Pokémon may belong to either one or two Egg Groups. Check out Bulbapedia for greater detail.",
  ];

  /**
   * Gets taxonomy endpoints.
   *
   * @return array
   *   taxonomy endpoints.
   */
  public final static function getTaxonomyEndpoints(): array {
    return self::$taxonomyEndpoints;
  }

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * PokemonManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   An entity type manager.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getResourceList(string $endpoint, int $limit): array {
    $api = new PokeApi;

    $resource_list_json = $api->resourceList($endpoint, $limit);
    return json_decode($resource_list_json, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function createTaxonomy(string $endpoint, string $description, array $list): ?string {
    $endpoint = str_replace('-', ' ', $endpoint);
    $endpoint .= ' API';
    $vid = str_replace(' ', '_', $endpoint);
    $name = ucfirst($endpoint);

    $vocabularies = Vocabulary::loadMultiple();
    if (!isset($vocabularies[$vid])) {
      $vocabulary = Vocabulary::create([
        'vid' => $vid,
        'description' => $description,
        'name' => $name,
      ])->save();

      $categories = [];
      foreach ($list['results'] as $ability) {
        $categories[] = $ability['name'];
      }

      foreach ($categories as $category) {
        $term = $this->entityTypeManager
          ->getStorage('taxonomy_term')
          ->create([
            'name' => $category,
            'vid' => $vid,
          ])
          ->save();
      }

      return $vid;
    }
    return NULL;
  }

  public function createContent(): ?string {
    $content_type = \Drupal\node\Entity\NodeType::create([
      'type' => 'pokemon_api',
      'name' => 'Pokemon API',
      'description' => 'Pokemon type',
    ]);
    $content_type->save();

    $node = Node::create([
      'type' => 'pokemon_api',
      'title' => 'Node created with fields',
      'body' => 'This is body field content',
      'field_name' => 'This is name of text field',
      'field_ability' => [
        ['target_id' => 1],
      ],
    ]);
    $node->save();

    return '';
  }

}
