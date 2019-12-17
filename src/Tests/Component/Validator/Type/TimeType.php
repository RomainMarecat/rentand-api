<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class TimeType extends Constraint
{
    private $targetEntity;
    private $message = "time.is_time.invalidate";
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
        return $this->values['time'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return is_time($value);
    }
}
