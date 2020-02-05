<?php

namespace App\Controller\Front;

use App\Entity\Session;
use App\Entity\User;
use App\Manager\SessionManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class SessionController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSessionsByUser"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sessions/users/{user}")
     * @param                     $user
     *
     * @param SessionManager $sessionManager
     * @return Session[]
     */
    public function getSessionsByUserAction(User $user, SessionManager $sessionManager)
    {
        return $sessionManager->getSessionsByUser($user);
    }
}
