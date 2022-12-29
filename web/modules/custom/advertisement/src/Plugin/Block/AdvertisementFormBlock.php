<?php

namespace Drupal\advertisement\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an Advertisement form block.
 *
 * @Block(
 *   id = "advertisement_advertisement_form_block",
 *   admin_label = @Translation("Advertisement Form Block"),
 *   category = @Translation("Trainee custom block")
 * )
 */
class AdvertisementFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\advertisement\Form\AdvertisementForm');
  }

}
