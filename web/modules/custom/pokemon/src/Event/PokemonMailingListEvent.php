<?php

namespace Drupal\pokemon\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event for obtaining entity updates.
 */
class PokemonMailingListEvent extends Event {

  /**
   * Called during hook_node_update().
   *
   * @var string
   */
  const UPDATE_POKEMON_NODE = 'pokemon.node_update';

  /**
   * Called during hook_taxonomy_term_update().
   *
   * @var string
   */
  const UPDATE_POKEMON_TAX_TERM = 'pokemon.taxonomy_term_update';

  /**
   * Called during hook_media_update().
   *
   * @var string
   */
  const UPDATE_POKEMON_MEDIA = 'pokemon.media_update';

  /**
   * The entity from a hook.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * PokemonMailingListEvent constructor.
   */
  public function __construct($entity) {
    $this->entity = $entity;
  }

  /**
   * Returns the entity from hooks.
   */
  public function getEntity() {
    return $this->entity;
  }

}
