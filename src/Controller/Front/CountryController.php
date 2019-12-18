<?php

namespace App\Controller\Front;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CountryController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getCountries"})
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
     * @Annotations\View(serializerGroups={"getCountriesByAlpha"})
     * @Annotations\Get("/countries/alpha/{alpha2}")
     * @param                        $alpha2
     * @param EntityManagerInterface $entityManager
     *
     * @return
     */
    public function getCountriesByAlphaAction($alpha2, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Country::class)->findByAlpha2($alpha2);
    }
}
