<?php

namespace App\Controller\Restricted;

use App\Manager\UserManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"patchUsers"})
     * @Annotations\Patch("/users")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return mixed
     */
    public function patchUsersAction(Request $request, UserManager $userManager)
    {
        $user = $this->getUser();
        return $userManager->patch($request, $user->getUsername());
    }
}
