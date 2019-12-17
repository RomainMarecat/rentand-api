<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class OnetomanyType extends Constraint
{
    private $targetEntity;
    private $message = "onetomany.is_onetomany.invalidate";
    private $values = [
                        'onetomany' => [
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
        return $this->values['onetomany'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return $this->isOnetomany($value);
    }

    public function isOnetomany($value)
    {
        if (!is_string($value) && !is_object($value)) {
            return false;
        }
        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        return $collection instanceof $value;
    }
}
