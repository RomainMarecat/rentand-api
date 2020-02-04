<?php

namespace App\Controller\Front;

use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class SportController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSports"})
     * @Annotations\Get("/sports")
     */
    public function getSportsAction()
    {
        return $this->getDoctrine()
            ->getRepository(Sport::class)
            ->findAll();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSportSpecialities"})
     * @Annotations\Get("/sports/{sport}/{advert}/specialities")
     */
    public function getSportSpecialitiesAction($sport, $user)
    {
        $specialities = $this->get('manager.sport')->getSpecialities($sport, $user);

        return $specialities;
    }

    /**
     * @Annotations\View(serializerGroups={"getSports"})
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
