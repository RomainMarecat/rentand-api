<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class ArrayType extends Constraint
{
    private $targetEntity;
    private $message = "array.is_array.invalidate";
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
        return $this->values['array'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return is_array($value);
    }
}
