<?php

namespace App\Controller\Front;

use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class LanguageController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getLanguages"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/languages")
     * @param EntityManagerInterface $entityManager
     *
     * @return Language[]|object[]
     */
    public function getLanguagesAction(EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Language::class)->findAll();
    }

    /**
     * @Annotations\View(serializerGroups={"getLanguagesByAlpha"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/languages/alpha/{alpha2}")
     * @param                        $alpha2
     * @param EntityManagerInterface $entityManager
     *
     * @return
     */
    public function getLanguagesByAlphaAction($alpha2, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Language::class)->findByAlpha2($alpha2);
    }
}
