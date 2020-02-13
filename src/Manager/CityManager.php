<?php

namespace App\Manager;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CityManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class CityManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCitiesByKeywords(string $keywords)
    {
        return $this->entityManager->getRepository(City::class)->findCitiesByKeywords($keywords);
    }
}
