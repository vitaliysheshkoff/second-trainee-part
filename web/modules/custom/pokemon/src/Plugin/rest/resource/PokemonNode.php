<?php

namespace Drupal\pokemon\Plugin\rest\resource;

use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Represents pokemon node as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_node",
 *   label = @Translation("Pokemon resource: node"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon/{id}",
 *   }
 * )
 */
class PokemonNode extends PokemonResource {

  /**
   * Responds to GET requests.
   *
   * @param int $id
   *   The ID of the record.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   */
  public function get($id) {
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $node = $this->entityTypeManager->getStorage('node')->load($id);
    if ($node && $node->bundle() == 'pokemon') {
      $response_node = $this->getFields($node);
      if ($response_node) {
        return new ResourceResponse($response_node);
      }
    }
    else {
      $response['message'] = 'Pokemon node with provided ID is not found.';

      return new ResourceResponse($response, 400);
    }
  }

}
