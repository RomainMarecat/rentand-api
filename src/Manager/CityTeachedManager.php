<?php

namespace App\Manager;

use App\Entity\CityTeached;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CityTeachedManager
{
    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getCitiesTeachedByUser(User $user)
    {
        return $this->em->getRepository(CityTeached::class)->findByUser($user);
    }
}
