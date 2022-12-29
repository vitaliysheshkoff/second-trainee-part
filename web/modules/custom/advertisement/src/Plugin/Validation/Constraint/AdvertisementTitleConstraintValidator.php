<?php

namespace Drupal\advertisement\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Advertisement Title Constraint constraint.
 */
class AdvertisementTitleConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {

    if (is_string($items)) {
      if (strlen($items) < 20) {
        $this->context->addViolation($constraint->message);
      }
      return;
    }

    if (!isset($items) || $items->isEmpty()) {
      return;
    }

    foreach ($items as $item) {
      $title = $item->getValue()['value'];

      if (strlen($title) < 20) {
        $this->context->addViolation($constraint->message);
      }
    }
  }

}
