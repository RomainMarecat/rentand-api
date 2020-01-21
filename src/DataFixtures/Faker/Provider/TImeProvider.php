<?php

namespace App\DataFixtures\Faker\Provider;

use Faker\Provider\Base as BaseProvider;

class TImeProvider extends BaseProvider
{
    public static function startTime()
    {
        return (new \DateTime('now'))->setTime(8, 00, 00);
    }

    public static function endTime()
    {
        return (new \DateTime('now'))->setTime(19, 00, 00);
    }
}
