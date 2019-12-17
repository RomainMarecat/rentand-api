<?php

namespace App\DataFixtures\Faker\Provider;

use Faker\Provider\Base as BaseProvider;

class LowerCaseTextProvider extends BaseProvider
{
    /**
     * @return string Random voucher type
     */
    public static function lowerCaseText()
    {
        return strtolower(self::countryCode());
    }
}
