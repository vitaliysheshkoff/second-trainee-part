<?php

namespace Drupal\pokemon\Plugin\rest\resource;

use Drupal\media\Entity\Media;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Represents pokemon images as the resource.
 *
 * @RestResource (
 *   id = "pokemon_pokemon_images",
 *   label = @Translation("Pokemon resource: images"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/pokemon-images",
 *   }
 * )
 */
class PokemonImages extends PokemonResource {

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

    $mids = $this->entityTypeManager->getStorage('media')->getQuery()
      ->accessCheck(FALSE)
      ->condition('bundle', 'pokemon_image')
      ->execute();

    $medias = Media::loadMultiple($mids);

    $response_medias = [];
    foreach ($medias as $media) {
      $image_uri = $media->get('field_media_image_1')->entity->getFileUri();
      $media_url = $this->fileUrlGenerator
        ->generateAbsoluteString($image_uri);

      $response_medias[] = [
        'name' => $media->getName(),
        'id' => $media->id(),
        'url' => $media_url,
      ];
    }

    $count = sizeof($response_medias);
    if ($response_medias) {
      return new ResourceResponse([
        'count' => $count,
        'pokemon_image' => $response_medias,
      ]);
    }
    else {
      $response['message'] = 'Pokemons were not found.';
      return new ResourceResponse($response, 400);
    }

  }

}
