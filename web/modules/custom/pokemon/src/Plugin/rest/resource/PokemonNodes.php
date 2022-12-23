<?php

namespace Drupal\pokemon\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Represents pokemon nodes as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_nodes",
 *   label = @Translation("Pokemon resource: nodes"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemons",
 *   }
 * )
 */
class PokemonNodes extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get() {
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $nids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'pokemon')
      ->execute();

    $nodes = Node::loadMultiple($nids);

    foreach ($nodes as $node) {
      $response_nodes[] = $this->getFields($node);
    }

    if ($response_nodes) {
      return new ResourceResponse($response_nodes);
    }
    else {
      $response['message'] = 'Pokemons were not found.';
      return new ResourceResponse($response, 400);
    }
  }

}
