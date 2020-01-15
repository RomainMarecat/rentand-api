<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Manager\SessionManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class SessionController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSessionsByUser"})
     * @Annotations\Get("/sessions/users/{user}")
     * @param                     $user
     *
     * @param SessionManager $sessionManager
     * @return
     */
    public function getSessionsByUserAction(User $user, SessionManager $sessionManager)
    {
        return $sessionManager->getSessionsByUser($user);
    }
}
