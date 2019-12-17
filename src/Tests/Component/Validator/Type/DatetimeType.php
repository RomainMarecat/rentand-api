<?php

namespace App\Tests\Component\Validator\Type;

use Symfony\Component\Validator\Constraint;

class DatetimeType extends Constraint
{
    private $targetEntity;
    private $message = "datetime.is_datetime.invalidate";
    private $values = [
                        'datetime' => [
                            0 => 9,
                            1 => ['value' => '2012-02-30 12:12:12', 'format' => 'Y-m-d H:i:s', 'return' => false],
                            2 => ['value' => '2012-02-28', 'format' => 'Y-m-d', 'return' => false],
                            3 => ['value' => '28/02/2012', 'format' => 'd/m/Y', 'return' => false],
                            4 => ['value' => '30/02/2012', 'format' => 'd/m/Y', 'return' => false],
                            5 => ['value' => '14:50', 'format' => 'H:i', 'return' => false],
                            6 => ['value' => '14:77', 'format' => 'H:i', 'return' => false],
                            7 => ['value' => 14, 'format' => 'H', 'return' => false],
                            8 => ['value' => '14', 'format' => 'H', 'return' => false],
                            9 => ['value' => '2012-02-28 12:12:12', 'format' => 'Y-m-d H:i:s', 'return' => false]
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
        return $this->values['datetime'][0];
    }

    public function isValidateConstraint($value, $format = null)
    {
        return $this->isDatetime($value, $format);
    }

    public function isDatetime($value, $format = 'Y-m-d H:i:s')
    {
        $date = new \DateTime();
        $d = $date::createFromFormat($format, date_format($value, $format));
        return $d && $d->format($format) == $value;
    }
}
