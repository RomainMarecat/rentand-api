<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Manager\CityTeachedManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class CityTeachedController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getCity"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/cities_teached/cities/user_id/{user}")
     * @param User               $user
     * @param CityTeachedManager $cityTeachedManager
     *
     * @return mixed
     */
    public function getCitiesByUserAction(User $user, CityTeachedManager $cityTeachedManager)
    {
        return $cityTeachedManager->getCitiesTeachedByUser($user);
    }
}