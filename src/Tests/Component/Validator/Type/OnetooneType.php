<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class OnetooneType extends Constraint
{
    private $targetEntity;
    private $message = "onetoone.is_onetoone.invalidate";
    private $values = [
                        'onetoone' => [
                            0 => 0,
                            1 => ['value' => '', 'format' => null, 'return' => true]
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
        return $this->values['onetoone'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return $this->isOnetoone($value);
    }

    public function isOnetoone($value)
    {
        $targetEntity = $this->getTargetEntity();
        return $value instanceof $targetEntity;
    }

    public function setTargetEntity($targetEntity)
    {
        $this->targetEntity = $targetEntity;
        return $this;
    }

    public function getTargetEntity()
    {
        return $this->targetEntity;
    }
}
