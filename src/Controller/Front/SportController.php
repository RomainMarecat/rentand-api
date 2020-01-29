<?php

namespace App\Controller\Front;

use App\Entity\Sport;
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
     * @Annotations\View(serializerGroups={"Default", "getSport"})
     * @Annotations\Get("/sports/{sport}")
     */
    public function getSportAction($sport)
    {
        return $this->get("manager.sport")->get($sport);
    }
}
