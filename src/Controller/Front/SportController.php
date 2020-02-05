<?php

namespace App\Controller\Front;

use App\Entity\Sport;
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
