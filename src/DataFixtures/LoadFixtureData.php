<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

final class LoadFixtureData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Pass $this as an additional faker provider to make the "groupName"
        // method available as a data provider
        Fixtures::load(__DIR__ . '/User.yml', $manager, ['providers' => [$this]]);
    }

    /**
     * @return array
     */
    public static function getRoleUser()
    {
        $value = (bool) rand(0, 1);
        if ($value) {
            return ['ROLE_USER', 'ROLE_PART'];
        }

        return ['ROLE_USER', 'ROLE_MONO'];
    }
}
