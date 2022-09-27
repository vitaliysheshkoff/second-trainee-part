<?php

namespace Drupal\trainee_user\Controller;

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
    $userList = $this->userManager->getList($page);

    return [
      '#theme' => 'trainee_user_list',
      '#users' => $userList,
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
      '#routes' => [
        'add' => 'trainee_user.management_form',
        'delete' => 'trainee_user.delete_form',
      ],
      '#page' => $page,
      '#attached' => ['library' => ['trainee_user/table-style']],
    ];
  }

}
