<?php

namespace App\Controller\Front;

use App\Manager\CityManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CityController extends AbstractFOSRestController
{
    /**
     * Find cities
     *
     * @Annotations\View(serializerGroups={"getCities"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/cities/keywords/{keywords}")
     * @param CityManager $cityManager
     * @param string $keywords
     * @return mixed
     */
    public function getCitiesByKeywordsAction(CityManager $cityManager, string $keywords)
    {
        return $cityManager->getCitiesByKeywords($keywords);
    }
}
