<?php

namespace Drupal\trainee_user\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\trainee_user\UserManagerService;
use Laminas\Diactoros\Response\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
   * UserController constructor.
   *
   * @param \Drupal\trainee_user\UserManagerService $user_manager
   *   The user manager.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   The kill switch.
   */
  public function __construct(UserManagerService $user_manager, KillSwitch $killSwitch) {
    $this->userManager = $user_manager;
    $this->killSwitch = $killSwitch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): UserController {
    return new static(
      $container->get('trainee_user.user_manager_service'),
      $container->get('page_cache_kill_switch'),
    );
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

    $add_path = Url::fromRoute('trainee_user.management_form')
      ->setRouteParameters([
        'page' => $page,
      ]);

    foreach ($user_list as &$user) {
      if (isset($user['id'])) {
        $user['delete_path'] = Url::fromRoute('trainee_user.delete_form')
          ->setRouteParameters([
            'page' => $page,
            'id' => $user['id'],
          ]);
        $user['update_path'] = Url::fromRoute('trainee_user.management_form')
          ->setRouteParameters([
            'page' => $page,
            'id' => $user['id'],
          ]);
      }
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
   * Provides handle autocomplete.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Laminas\Diactoros\Response\JsonResponse
   *   The response.
   */
  public function handleEmailAutocomplete(Request $request): JsonResponse {

    $string = $request->query->get('q');

    $matches = [];
    $emails = [];

    if ($string) {
      $input = preg_quote($string, '~');

      $user_list = $this->userManager->getList(1);

      foreach ($user_list as $user) {
        $emails[] = $user['email'];
      }

      $suitable_emails = preg_grep('~' . $input . '~', $emails);

      foreach ($suitable_emails as $email) {
        $matches[] = ['value' => $email, 'label' => $email];
      }
    }
    return new JsonResponse($matches);
  }

}
