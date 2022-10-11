<?php

namespace Drupal\trainee_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\trainee_user\Controller\UserController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with user table.
 *
 * @Block(
 *   id = "trainee_user_user_table_block",
 *   admin_label = @Translation("User table block"),
 * )
 */
class UserTableBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The user controller.
   *
   * @var \Drupal\trainee_user\Controller\UserController
   */
  protected UserController $userController;

  /**
   * Constructs a new UserTableBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\trainee_user\Controller\UserController $userController
   *   The user controller.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UserController $userController) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->userController = $userController;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): UserTableBlock {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('trainee_user.user_controller'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'page' => 1,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['page'] = [
      '#type' => 'number',
      '#min' => 1,
      '#title' => t('What page of users'),
      '#default_value' => $config['page'] ?? 1,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $page = $form_state->getValue('page');

    if (!is_numeric($page) || $page < 1) {
      $form_state->setErrorByName('page', t('Needs to be an integer and more or equal 1.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['page'] = $form_state->getValue('page');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = $this->getConfiguration();

    return $this->userController->showUserList($config['page'] ?? 1);
  }

}
