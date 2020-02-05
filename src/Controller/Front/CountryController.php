<?php

namespace App\Controller\Front;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CountryController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getCountries"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/countries")
     * @param EntityManagerInterface $entityManager
     *
     * @return Country[]|object[]
     */
    public function getCountriesAction(EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Country::class)->findAll();
    }

    /**
     * @Annotations\View(serializerGroups={"getCountriesByAlpha"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/countries/alpha/{alpha2}")
     * @param string $alpha2
     * @param EntityManagerInterface $entityManager
     *
     * @return Country[]
     */
    public function getCountriesByAlphaAction(string $alpha2, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Country::class)->findByAlpha2($alpha2);
    }
}
