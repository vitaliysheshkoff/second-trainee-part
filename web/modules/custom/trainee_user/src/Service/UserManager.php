<?php

namespace Drupal\trainee_user\Service;

use Drupal\Core\Session\AccountInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class UserManager
 * @package Drupal\traomee_user\Service
 */
class UserManager
{

  protected $currentUser;

  /**
   * UserManager constructor.
   * @param AccountInterface $currentUser
   */
  public function __construct(AccountInterface $currentUser)
  {
    $this->currentUser = $currentUser;
  }

  //  this method work

  /**
   * @throws GuzzleException
   */
  public function getUsers(int $page)
  {
    $client = new Client();
    $response = $client->request('GET', "https://gorest.co.in/public/v2/users/?access-token=" . getenv('ACCESS_TOKEN')/*"https://gorest.co.in/public/v2/users"*/, [
        'query' => [
          'page' => $page,
        ],
      ]
    );

    if ($response->getStatusCode() <= 400) {
      // return json_decode($response->getBody());
      return $response->getBody();
    }

    return null;
  }

  // this method work for all users

  /**
   * @throws GuzzleException
   */
  public function getUser($id)
  {
    $client = new Client();
    $response = $client->request('GET', "https://gorest.co.in/public/v2/users/$id?access-token=" . getenv('ACCESS_TOKEN')
    );

    if ($response->getStatusCode() <= 400) {
      // return json_decode($response->getBody());
      return $response->getBody();
    }

    return null;
  }


  // this method work fine

  /**
   * @throws GuzzleException
   */
  public function createUser()
  {
    $client = new Client();
    $response = $client->request('POST', "https://gorest.co.in/public/v2/users?access-token=" . getenv('ACCESS_TOKEN') /*'https://gorest.co.in/public/v2/users'*/, [
        'form_params' => [
          'name' => 'Aawenali Ramakrishna',
          'gender' => 'male',
          'email' => 'xqwweeenali.ramakrishna@15ce.com',
          'status' => 'active'
        ]
      ]
    );

    if ($response->getStatusCode() <= 400) {
      // return json_decode($response->getBody());
      return $response->getBody();
    }

    return null;
  }

  //this method work fine

  /**
   * @throws GuzzleException
   */
  public function deleteUser(int $id)
  {
    $client = new Client();
    $response = $client->request('DELETE', "https://gorest.co.in/public/v2/users/$id?access-token=" . getenv('ACCESS_TOKEN'));
    if ($response->getStatusCode() <= 400) {
      // return json_decode($response->getBody());
      return $response->getStatusCode();
    }
    return null;
  }

  //this method work fine

  /**
   * @throws GuzzleException
   */
  public function updateUser(int $id, string $name, string $gender, string $email, string $status)
  {
    $client = new Client();
    $response = $client->request('PUT', "https://gorest.co.in/public/v2/users/$id?access-token=" . getenv('ACCESS_TOKEN'), [
        'form_params' => [
          'name' => $name,
          'gender' => $gender,
          'email' => $email,
          'status' => $status
        ]
      ]
    );

    if ($response->getStatusCode() <= 400) {
      // return json_decode($response->getBody());
      return $response->getBody();
    }
    return null;
  }

}
