<?php

namespace Drupal\trainee_user;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


/**
 * Class UserManager
 * @package Drupal\trainee_user
 */
class UserManager implements ManagerInterface {

  /**
   * @throws GuzzleException
   */
  public function getList(int $page): ?array {
    $client = new Client();
    $response = $client->request('GET', "https://gorest.co.in/public/v2/users/?access-token="
      . getenv('ACCESS_TOKEN'), [
        'query' => [
          'page' => $page,
        ],
      ]
    );

    if ($response->getStatusCode() <= 400) {
      return json_decode($response->getBody());
    }

    return null;
  }

  /**
   * @throws GuzzleException
   */
  public function get(int $id): object|null {
    $client = new Client();
    $response = $client->request('GET', "https://gorest.co.in/public/v2/users/$id?access-token="
      . getenv('ACCESS_TOKEN')
    );

    if ($response->getStatusCode() <= 400) {
      return json_decode($response->getBody());
    }

    return null;
  }

  public function update(int $id): object|null {
    $client = new Client();
    $response = $client->request('PUT', "https://gorest.co.in/public/v2/users/$id?access-token="
      . getenv('ACCESS_TOKEN'), [
        'form_params' => [
          'name' => 'Bawenali Ramakrishna',
          'gender' => 'male',
          'email' => 'pavloereae@mail.com',
          'status' => 'active'
        ]
      ]
    );

    if ($response->getStatusCode() <= 400) {
      return json_decode($response->getBody());
    }
    return null;
  }

  /**
   * @throws GuzzleException
   */
  public function delete(int $id): int|null {
    $client = new Client();
    $response = $client->request('DELETE', "https://gorest.co.in/public/v2/users/$id?access-token="
      . getenv('ACCESS_TOKEN'));

    if ($response->getStatusCode() <= 400) {
      return $response->getStatusCode();
    }
    return null;
  }

  /**
   * @throws GuzzleException
   */
  public function create(): object|null {
    $client = new Client();
    $response = $client->request('POST', "https://gorest.co.in/public/v2/users?access-token="
      . getenv('ACCESS_TOKEN') /*'https://gorest.co.in/public/v2/users'*/, [
        'form_params' => [
          'name' => 'Aawenali Ramakrishna',
          'gender' => 'male',
          'email' => 'aerrrxqwweeenali.ramakrishna@15ce.com',
          'status' => 'active'
        ]
      ]
    );

    if ($response->getStatusCode() <= 400) {
      return json_decode($response->getBody());
    }

    return null;
  }
}
