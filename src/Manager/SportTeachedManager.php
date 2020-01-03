<?php

namespace App\Manager;

use App\Entity\SportTeached;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SportTeachedManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class SportTeachedManager
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSportsTeachedByUser($user)
    {
        return $this->entityManager
            ->getRepository(SportTeached::class)
            ->findBy(['user' => $user]);
    }
}
