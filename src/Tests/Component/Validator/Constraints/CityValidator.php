<?php

namespace App\Tests\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CityValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint, $format = null)
    {
        if (!$constraint->isValidateConstraint($value, $format = null)) {
            $this->context->addViolation($constraint->getMessage());
        }
    }
}
