<?php

namespace Drupal\pokemon\EventSubscriber;

use Drupal\pokemon\Event\PokemonMailingListEvent;

/**
 * Pokemon update taxonomy term event subscriber.
 */
class PokemonUpdateTaxTermSubscriber extends PokemonEntityUpdateBaseSubscriber {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PokemonMailingListEvent::UPDATE_POKEMON_TAX_TERM => [
        'updatePokemonTaxTerm',
      ],
    ];
  }

  /**
   * Subscriber function  for PokemonMailingListEvent::UPDATE_POKEMON_TAX_TERM.
   *
   * @param \Drupal\pokemon\Event\PokemonMailingListEvent
   */
  public function updatePokemonTaxTerm(PokemonMailingListEvent $event) {
    $this->updateEntity($event);
  }

}
