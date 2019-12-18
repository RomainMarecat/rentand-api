<?php

namespace App\Controller\Front;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class SportController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getSports"})
     * @Annotations\Get("/sports")
     */
    public function getSportsAction()
    {
        $sports = new ArrayCollection($this->getDoctrine()
            ->getRepository('App:Sport')
            ->findByParent(null));

        return array('sports' => $sports);
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
