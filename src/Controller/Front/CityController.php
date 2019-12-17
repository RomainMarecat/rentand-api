<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CityController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getCity"})
     */
    public function getCityAction($city)
    {
        $city = $this->get('manager.city')->get($city);

        return $city;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getCityGoogleId"})
     * @Annotations\Get("/cities/{city}/googleId")
     */
    public function getCityGoogleIdAction($city)
    {
        $city = $this->get('manager.city')->getGoogleId($city);

        return $city;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getCityByGoogleId"})
     * @Annotations\Get("/cities/googleId/{googleId}")
     */
    public function getCityByGoogleIdAction($googleId)
    {
        $city = $this->get('manager.city')->getCityByGoogleId($googleId);

        return $city;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getCityMeetings"})
     * @Annotations\Get("/cities/{city}/meetings")
     */
    public function getCityMeetingsAction($city)
    {
        $meetings = $this->get('manager.city')->getMeetings($city);

        return $meetings;
    }
}
