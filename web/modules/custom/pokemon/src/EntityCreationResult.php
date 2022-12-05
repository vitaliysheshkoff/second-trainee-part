<?php

namespace Drupal\pokemon;

/**
 * Helper class to store the result of creation of the entity.
 */
class EntityCreationResult {

  /**
   * The status of the creation.
   *
   * @var string
   */
  private string $status;

  /**
   * The entity instance.
   */
  private $entity;

  /**
   * Constructs a EntityCreationResult class.
   *
   * @param string $status
   *   The status from creation.
   * @param  $entity
   *   The entity or NULL.
   */
  public function __construct(string $status, $entity = NULL) {
    $this->status = $status;
    $this->entity = $entity;
  }

  /**
   * Gets the status.
   *
   * @return string
   *   Return the status value.
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Gets the entity.
   */
  public function getEntity() {
    return $this->entity;
  }

}
