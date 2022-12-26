<?php

namespace Drupal\pokemon\EventSubscriber;

use Drupal\pokemon\Event\PokemonMailingListEvent;

/**
 * Pokemon update media event subscriber.
 */
class PokemonUpdateMediaSubscriber extends PokemonEntityUpdateBaseSubscriber {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PokemonMailingListEvent::UPDATE_POKEMON_MEDIA => [
        'updatePokemonMedia',
      ],
    ];
  }

  /**
   * Subscriber function  for PokemonMailingListEvent::UPDATE_POKEMON_MEDIA.
   *
   * @param \Drupal\pokemon\Event\PokemonMailingListEvent $event
   */
  public function updatePokemonMedia(PokemonMailingListEvent $event) {
    $this->updateEntity($event);
  }

}
