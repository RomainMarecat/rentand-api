<?php

namespace App\Controller\Front;

use App\Entity\Sport;
use App\Manager\SportManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class SportController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSports"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports")
     */
    public function getSportsAction()
    {
        return $this->getDoctrine()
            ->getRepository(Sport::class)
            ->findAll();
    }

    /**
     * @Annotations\View(serializerGroups={"getSportSpecialities"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports/{sport}/{advert}/specialities")
     * @param $sport
     * @param $user
     * @param SportManager $sportManager
     * @return
     */
    public function getSportSpecialitiesAction($sport, $user, SportManager $sportManager)
    {
        return $sportManager->getSpecialities($sport, $user);
    }

    /**
     * @Annotations\View(serializerGroups={"getSports"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports/{slug}")
     * @param $slug
     * @param EntityManagerInterface $entityManager
     * @return
     */
    public function getSportAction($slug, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Sport::class)
            ->findOneBySlug($slug);
    }
}
