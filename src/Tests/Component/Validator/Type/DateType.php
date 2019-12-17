<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class DateType extends Constraint
{
    private $targetEntity;
    private $message = "date.is_date.invalidate";
    private $values = [
                        'date' => [
                            0 => 5,
                            1 => ['value' => '2012-02-30', 'format' => 'Y-m-d', 'return' => false],
                            2 => ['value' => '2012-02-28', 'format' => 'Y-m-d', 'return' => false],
                            3 => ['value' => '28/02/2012', 'format' => 'd/m/Y', 'return' => false],
                            4 => ['value' => '30/02/2012', 'format' => 'd/m/Y', 'return' => false],
                            5 => ['value' => '2012-02-28', 'format' => 'Y-m-d', 'return' => false]
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
        return $this->values['date'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return $this->isDate($value, $format);
    }

    public function isDate($value, $format = 'Y-m-d H:i:s')
    {
        $date = new \DateTime();
        $d = $date::createFromFormat($format, date_format($value, $format));
        return $d && $d->format($format) == $value;
    }
}
