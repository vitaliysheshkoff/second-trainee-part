<?php

namespace Drupal\advertisement\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides Advertisement Title Constraint.
 *
 * @Constraint(
 *   id = "AdvertisementTitleConstraint",
 *   label = @Translation("Advertisement Title Constraint", context = "Validation"),
 * )
 *
 * @DCG
 * To apply this constraint, see https://www.drupal.org/docs/drupal-apis/entity-api/entity-validation-api/providing-a-custom-validation-constraint.
 */
class AdvertisementTitleConstraint extends Constraint {

  /**
   * Constraint message.
   *
   * @var string
   */
  public $message = 'The title must be at least 20 characters long.';

}
