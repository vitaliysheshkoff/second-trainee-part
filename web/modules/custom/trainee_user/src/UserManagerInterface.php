<?php

namespace Drupal\trainee_user;

/**
 * Defines an interface for classes that handle CRUD operations with the API.
 */
interface UserManagerInterface {

  /**
   * Provides to get a list of records.
   *
   * @param int $page
   *   Api page number.
   *
   * @return array|null
   *   A structured array containing all page records.
   */
  public function getList(int $page): ?array;

  /**
   * Provides to get a record.
   *
   * @param int $id
   *   Record id.
   *
   * @return array|null
   *   Record array.
   */
  public function get(int $id): ?array;

  /**
   * Provides to update a record.
   *
   * @param int $id
   *   Record id.
   * @param array $record
   *   Record array.
   *
   * @return array|null
   *   Updated record array.
   */
  public function update(int $id, array $record): ?array;

  /**
   * Provides to delete a record.
   *
   * @param int $id
   *   Record id.
   *
   * @return int|null
   *   Status code of operation.
   */
  public function delete(int $id): ?int;

  /**
   * Provides to create a new record.
   *
   * @param array $record
   *   Record array.
   *
   * @return array|null
   *   Created record array.
   */
  public function create(array $record): ?array;

}
