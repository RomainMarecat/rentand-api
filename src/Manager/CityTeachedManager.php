<?php

namespace App\Manager;

use App\Entity\CityTeached;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CityTeachedManager
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     *
     * @return CityTeached[]
     */
    public function getCitiesTeachedByUser(User $user)
    {
        return $this->entityManager->getRepository(CityTeached::class)->findByUser($user);
    }
}
