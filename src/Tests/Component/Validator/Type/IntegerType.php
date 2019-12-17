<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class IntegerType extends Constraint
{
    private $targetEntity;
    private $message = "integer.is_integer.invalidate";
    private $values = [
                        'integer' => [
                            0 => 7,
                            1 => ['value' => 1, 'format' => null, 'return' => true],
                            2 => ['value' => 23.5, 'format' => null, 'return' => false],
                            3 => ['value' => 1e7, 'format' => null, 'return' => false],
                            4 => ['value' => true, 'format' => null, 'return' => false],
                            5 => ['value' => '', 'format' => null, 'return' => false],
                            6 => ['value' => 0, 'format' => null, 'return' => true],
                            7 => ['value' => 0.0, 'format' => null, 'return' => false],
                            8 => ['value' => 1.0, 'format' => null, 'return' => true],
                            9 => ['value' => 'abc', 'format' => null, 'return' => false]
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
        return $this->values['integer'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return is_integer($value);
    }
}
