<?php

namespace Drupal\trainee_user;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\trainee_user\Form\UserApiConfigForm;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * User manager service for trainee_user module.
 */
class UserManagerService implements UserManagerInterface {

  /**
   * The client of API.
   *
   * @var \GuzzleHttp\Client
   */
  protected Client $client;

  /**
   * The API config.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Constructs the UserManagerService.
   */
  public function __construct(ConfigFactoryInterface $configFactory, Client $client) {
    $this->configFactory = $configFactory;
    $this->client = $client;
  }

  /**
   * {@inheritdoc}
   */
  public function getList(int $page): ?array {
    try {
      $response = $this->request('GET', ['query' => ['page' => $page]]);
    }
    catch (\Throwable) {
      return NULL;
    }
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * {@inheritdoc}
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function get(int $id): ?array {
    $response = $this->request('GET', ['id' => $id]);
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * {@inheritdoc}
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function update(int $id, array $record): ?array {
    $response = $this->request('PUT', ['id' => $id, 'record' => $record]);
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * {@inheritdoc}
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function delete(int $id): ?int {
    $response = $this->request('DELETE', ['id' => $id]);
    return $response->getStatusCode();
  }

  /**
   * {@inheritdoc}
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function create(array $record): ?array {
    $response = $this->request('POST', ['record' => $record]);
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * Provides response from REST API.
   *
   * @param string $method
   *   possible values: 'POST', 'GET', 'PUT', 'PATCH', DELETE'.
   * @param array $params
   *   Form params:
   *   array record(name, gender, email, status),
   *   array query,
   *   int id.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   response from API.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function request(string $method, array $params): ResponseInterface {
    $config = $this->configFactory->getEditable(UserApiConfigForm::SETTINGS);

    $url = $config->get('api_base_url') ?? '';
    $url .= ($params['id'] ?? '');

    if ($method == 'DELETE' || $method == 'GET') {
      return $this->client->request($method,
        $url, [
          'headers' => [
            'Accept' => $config->get('header_accept') ?? '',
            'Content-Type' => $config->get('header_content_type') ?? '',
            'Authorization' => " Bearer " . ($config->get('api_token') ?? ''),
          ],
          'query' => $params['query'] ?? [],
        ]
      );
    }
    else {
      return $this->client->request($method,
        $url . "?access-token="
        . ($config->get('api_token') ?? ''), [
          'form_params' => $params['record'] ?? [],
        ]
      );
    }
  }

}
