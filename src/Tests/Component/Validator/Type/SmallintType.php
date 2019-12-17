<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class SmallintType extends Constraint
{
    private $targetEntity;
    private $message = "smallint.is_smallint.invalidate";
    private $values = [
                    ];

    public function getMessage()
    {
        return $this->message;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getNbInvalidate()
    {
        return $this->values['smallint'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return is_smallint($value);
    }
}
