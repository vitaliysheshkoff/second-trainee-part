<?php

namespace Drupal\trainee_user;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

/**
 * Class UserManagerService
 * @package Drupal\trainee_user
 */
class UserManagerService implements ManagerInterface {
  public function getList(int $page): ?array {
    $client = new Client();
    try {
      $response = $client->request('GET',
        "https://gorest.co.in/public/v2/users/?access-token=" . getenv('ACCESS_TOKEN'), [
          'query' => [
            'page' => $page,
          ],
        ]
      );
    } catch (Throwable) {
      return null;
    }
    return json_decode($response->getBody(), true);
  }

  /**
   * @throws GuzzleException
   */
  public function get(int $id): ?array {
    $client = new Client();
    $response = $client->request('GET',
      "https://gorest.co.in/public/v2/users/$id?access-token=" . getenv('ACCESS_TOKEN'));
    return json_decode($response->getBody(), true);
  }

  /**
   * @throws GuzzleException
   */
  public function update(int $id, array $record): ?array {
    $client = new Client();
    $response = $client->request('PUT',
      "https://gorest.co.in/public/v2/users/$id?access-token=" . getenv('ACCESS_TOKEN'), [
        'form_params' => [
          'name' => $record['name'],
          'gender' => $record['gender'],
          'email' => $record['email'],
          'status' => $record['status'],
        ]
      ]
    );
    return json_decode($response->getBody(), true);
  }

  /**
   * @throws GuzzleException
   */
  public function delete(int $id): ?int {
    $client = new Client();
    $response = $client->request('DELETE',
      "https://gorest.co.in/public/v2/users/$id?access-token=" . getenv('ACCESS_TOKEN'));
    return $response->getStatusCode();
  }

  /**
   * @throws GuzzleException
   */
  public function create(array $record): ?array {
    $client = new Client();
    $response = $client->request('POST',
      "https://gorest.co.in/public/v2/users?access-token=" . getenv('ACCESS_TOKEN'), [
        'form_params' => [
          'name' => $record['name'],
          'status' => $record['status'],
          'gender' => $record['gender'],
          'email' => $record['email'],
        ]
      ]
    );
    return json_decode($response->getBody(), true);
  }
}
