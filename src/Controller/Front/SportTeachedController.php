<?php

namespace App\Controller\Front;

use App\Manager\SportTeachedManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class SportTeachedController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSportsTeachedByUser"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports_teached/sports/user_id/{user}")
     * @param                     $user
     * @param SportTeachedManager $sportTeachedManager
     *
     * @return
     */
    public function getSportsTeachedByUserAction($user, SportTeachedManager $sportTeachedManager)
    {
        return $sportTeachedManager->getSportsTeachedByUser($user);
    }
}
