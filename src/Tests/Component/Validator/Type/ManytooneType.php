<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class ManytooneType extends Constraint
{
    private $targetEntity;
    private $message = "manytoone.is_manytoone.invalidate";
    private $values = [
                        'manytoone' => [
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
        return $this->values['manytoone'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return $this->isManytoone($value);
    }

    public function isManytoone($value)
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
