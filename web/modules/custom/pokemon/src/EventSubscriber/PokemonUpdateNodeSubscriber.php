<?php

namespace Drupal\pokemon\EventSubscriber;

use Drupal\pokemon\Event\PokemonMailingListEvent;

/**
 * Pokemon update node event subscriber.
 */
class PokemonUpdateNodeSubscriber extends PokemonEntityUpdateBaseSubscriber {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PokemonMailingListEvent::UPDATE_POKEMON_NODE => [
        'updatePokemonNode',
      ],
    ];
  }

  /**
   * Subscriber function  for PokemonMailingListEvent::UPDATE_POKEMON_NODE.
   *
   * @param \Drupal\pokemon\Event\PokemonMailingListEvent
   */
  public function updatePokemonNode(PokemonMailingListEvent $event) {
    $this->updateEntity($event);
  }

}
