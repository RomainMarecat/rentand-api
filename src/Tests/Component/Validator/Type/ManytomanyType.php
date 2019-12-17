<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class ManytomanyType extends Constraint
{
    private $targetEntity;
    private $message = "manytomany.is_manytomany.invalidate";
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
        return $this->values['manytomany'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return is_manytomany($value);
    }
}
