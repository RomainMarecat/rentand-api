<?php

namespace App\Controller\Front;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

class SportController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getSports"})
     * @Annotations\Get("/sports")
     */
    public function getSportsAction()
    {
        $sports = new ArrayCollection($this->getDoctrine()
            ->getRepository('AppBundle:Sport')
            ->findByParent(null));

        return array('sports' => $sports);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSportSpecialities"})
     * @Annotations\Get("/sports/{sport}/{advert}/specialities")
     */
    public function getSportSpecialitiesAction($sport, $advert)
    {
        $specialities = $this->get('manager.sport')->getSpecialities($sport, $advert);

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
