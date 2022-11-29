<?php

namespace Drupal\trainee_site\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a hero block.
 *
 * @Block(
 *   id = "trainee_site_trainee_hero_block",
 *   admin_label = @Translation("Trainee Hero Block"),
 * )
 */
class HeroBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('Enter the title text'),
      '#default_value' => 'Title text',
    ];

    $form['body'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Body'),
      '#description' => $this->t('Enter the body text'),
      '#default_value' => 'Body text',
    ];

    $form['left_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Left button text'),
      '#description' => $this->t('Enter the left button text'),
      '#default_value' => 'Left button text',
    ];

    $form['right_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Right button text'),
      '#description' => $this->t('Enter the right button text'),
      '#default_value' => 'Right button text',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['body'] = $form_state->getValue('body');
    $this->configuration['left_button_text'] = $form_state->getValue('left_button_text');
    $this->configuration['right_button_text'] = $form_state->getValue('right_button_text');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = $this->getConfiguration();

    $build = [];

    $text = [
      'title' => $config['title'],
      'body' => $config['body'],
    ];

    $button_text = [
      'left_button_text' => $config['left_button_text'],
      'right_button_text' => $config['right_button_text'],
    ];

    $build['image'] = [
      '#theme' => 'hero_block',
      '#text' => $text,
      '#button_text' => $button_text,
    ];

    return $build;

  }

}
