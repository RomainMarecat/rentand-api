<?php

namespace App\Controller\Front;

use App\Manager\CityManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CityController extends AbstractFOSRestController
{
    /**
     * Find city by id
     *
     * @Annotations\View(serializerGroups={"getCity", serializerEnableMaxDepthChecks=true})
     * @param $city
     * @param CityManager $cityManager
     * @return
     */
    public function getCityAction($city, CityManager $cityManager)
    {
        return $cityManager->get($city);
    }

    /**
     * Find city googleId by city id
     *
     * @Annotations\View(serializerGroups={"getCityGoogleId", serializerEnableMaxDepthChecks=true})
     * @Annotations\Get("/cities/{city}/googleId")
     * @param $city
     * @param CityManager $cityManager
     * @return
     */
    public function getCityGoogleIdAction($city, CityManager $cityManager)
    {
        return $cityManager->getGoogleId($city);
    }

    /**
     * Find city by googleId
     *
     * @Annotations\View(serializerGroups={"getCityByGoogleId", serializerEnableMaxDepthChecks=true})
     * @Annotations\Get("/cities/googleId/{googleId}")
     * @param $googleId
     * @param CityManager $cityManager
     * @return
     */
    public function getCityByGoogleIdAction($googleId, CityManager $cityManager)
    {
        return $cityManager->getCityByGoogleId($googleId);
    }

    /**
     * Find Meetings by city
     *
     * @Annotations\View(serializerGroups={"getCityMeetings", serializerEnableMaxDepthChecks=true})
     * @Annotations\Get("/cities/{city}/meetings")
     * @param $city
     * @param CityManager $cityManager
     * @return
     */
    public function getCityMeetingsAction($city, CityManager $cityManager)
    {
        return $cityManager->getMeetings($city);
    }
}
