<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class FloatType extends Constraint
{
    private $targetEntity;
    private $message = "float.is_float.invalidate";
    private $values = [
                        'float' => [
                            0 => 4,
                            1 => ['value' => 23, 'format' => null, 'return' => false],
                            2 => ['value' => 23.5, 'format' => null, 'return' => true],
                            3 => ['value' => 1e7, 'format' => null, 'return' => true],
                            4 => ['value' => true, 'format' => null, 'return' => false],
                            5 => ['value' => 0, 'format' => null, 'return' => false],
                            6 => ['value' => 0.0, 'format' => null, 'return' => true],
                            7 => ['value' => 1.0, 'format' => null, 'return' => true],
                            8 => ['value' => 'abc', 'format' => null, 'return' => false]
                            ]
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
        return $this->values['float'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return $this->isFloat($value);
    }
    public function isFloat($value)
    {
        return is_float($value);
    }
}
