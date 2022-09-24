<?php

/**
 * @file
 * Contains \Drupal\trainee_user\UserManagerService.
 */

namespace Drupal\trainee_user;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class UserManagerService.
 */
class UserManagerService implements UserManagerInterface {

  /**
   * {@inheritdoc}
   */
  protected Client $client;

  /**
   * Constructs the UserManagerService.
   */
  public function __construct() {
    $this->client = new Client();
  }

  /**
   * {@inheritdoc}
   */
  public function getList(int $page): ?array {
    try {
      $response = $this->request([
        'method' => 'GET',
        'query' => ['page' => $page],
      ]);
    } catch (Throwable) {
      return NULL;
    }
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * {@inheritdoc}
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function get(int $id): ?array {
    $response = $this->request(['method' => 'GET', 'id' => $id]);
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * {@inheritdoc}
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function update(int $id, array $record): ?array {
    $response = $this->request([
      'method' => 'PUT',
      'record' => $record,
      'id' => $id,
    ]);
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * {@inheritdoc}
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function delete(int $id): ?int {
    $response = $this->request(['method' => 'DELETE', 'id' => $id]);
    return $response->getStatusCode();
  }

  /**
   * {@inheritdoc}
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function create(array $record): ?array {
    $response = $this->request(['method' => 'POST', 'record' => $record]);
    return json_decode($response->getBody(), TRUE);
  }


  /**
   * Provides response from REST API.
   *
   * @param array $params
   *   string method: 'POST', 'GET', 'PUT', 'PATCH', DELETE',
   *   array record(name, gender, email,status),
   *   array query,
   *   int id.
   *
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function request(array $params): ResponseInterface {

    $url = "https://gorest.co.in/public/v2/users/{$params['id']}";

    if ($params['method'] == 'DELETE' || $params['method'] == 'GET') {
      return $this->client->request($params['method'],
        $url, [
          'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => " Bearer " . getenv('ACCESS_TOKEN'),
          ],
          'query' => $params['query'],
        ]
      );
    }
    else {
      return $this->client->request($params['method'],
        $url . "?access-token="
        . getenv('ACCESS_TOKEN'), [
          'form_params' => [
            'name' => $params['record']['name'],
            'gender' => $params['record']['gender'],
            'email' => $params['record']['email'],
            'status' => $params['record']['status'],
          ],
        ]
      );
    }
  }

}
