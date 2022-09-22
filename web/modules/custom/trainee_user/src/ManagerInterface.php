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
   * @return array|null
   *   record array
   */
  public function get(int $id): ?array;

  /**
   * Return updated record
   *
   * @param int $id
   *   record id
   *
   * @param array $record
   *   record array
   *
   * @return array|null
   *   record array
   */
  public function update(int $id, array $record): ?array;

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
   * @param array $record
   *   record array
   *
   * @return array|null
   *   record array
   */
  public function create(array $record): ?array;

}
