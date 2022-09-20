<?php

namespace Drupal\trainee_user;

/**
 * Defines an interface for classes that handle communication with the API.
 */
interface ManagerInterface {
  /**
   * Return list of records
   *
   * @param int $page
   *   Api page number
   *
   * @return array|null
   *   A structured array containing all page records.
   *
   */
  public function getList(int $page): ?array;

  /**
   * Return record
   *
   * @param int $id
   *   record id
   *
   * @return object|null
   *   record
   */
  public function get(int $id): ?object;

  /**
   * Return updated record
   *
   * @param int $id
   *   record id
   *
   * @return object|null
   *   record
   */
  public function update(int $id): ?object;

  /**
   * Return status code of operation
   *
   * @param int $id
   *   record id
   *
   * @return int|null
   *   status code of operation
   */
  public function delete(int $id): ?int;

  /**
   * Return created record
   *
   * @return object|null
   *   record object
   */
  public function create(): ?object;

}
