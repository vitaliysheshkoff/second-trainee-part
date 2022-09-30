<?php

namespace Drupal\trainee_user\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\trainee_user\UserManagerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for trainee_user module.
 */
class UserController extends ControllerBase {

  /**
   * The user manager.
   *
   * @var \Drupal\trainee_user\UserManagerService
   */
  protected UserManagerService $userManager;

  /**
   * Page cache killer.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected KillSwitch $killSwitch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): UserController {
    $instance = parent::create($container);
    $instance->userManager = $container->get('trainee_user.user_manager_service');
    $instance->killSwitch = $container->get('page_cache_kill_switch');

    return $instance;
  }

  /**
   * Provides to show user list theme.
   *
   * @param int $page
   *   The page.
   *
   * @return array
   *   User list theme.
   */
  public function showUserList(int $page = 1): array {
    $this->killSwitch->trigger();

    $user_list = $this->userManager->getList($page);

    $add_path = $this->getUrl($page, 'trainee_user.management_form');

    foreach ($user_list as &$user) {
      $user['delete_path'] = $this->getUrl($page, 'trainee_user.delete_form', $user['id'] ?? -1);
      $user['update_path'] = $this->getUrl($page, 'trainee_user.management_form', $user['id'] ?? -1);
    }

    return [
      '#theme' => 'trainee_user_list',
      '#users' => $user_list,
      '#attributes' => [
        'button' => [
          'class' => 'button button--link',
        ],
      ],
      '#button_text' => [
        'add' => 'add new user',
        'delete' => 'delete',
        'edit' => 'edit',
      ],
      '#table' => [
        'ID',
        'NAME',
        'EMAIL',
        'GENDER',
        'STATUS',
        'ACTION',
      ],
      '#add_path' => $add_path,
      '#attached' => ['library' => ['trainee_user/table-style']],
    ];
  }

  /**
   * Provides to get URL by route.
   *
   * @param int $page
   *   The page.
   * @param string $route
   *   The route.
   * @param int|null $id
   *   (optional)The id.
   *
   * @return \Drupal\Core\Url
   *   The Url from route.
   */
  private function getUrl(int $page, string $route, int $id = NULL): Url {

    $route_params = [
      'page' => $page,
    ];

    if ($id !== NULL) {
      $route_params += ['id' => $id];
    }

    return Url::fromRoute($route)->setRouteParameters($route_params);
  }

}
