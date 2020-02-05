<?php

namespace App\Controller\Restricted;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AccountController extends AbstractFOSRestController
{
    /**
     * @Annotations\Get("/users/account")
     * @Security("has_role('ROLE_USER')")
     * @Annotations\View(serializerGroups={"getAccount"}, serializerEnableMaxDepthChecks=true)
     * @param EntityManagerInterface $entityManager
     *
     * @return object|null
     */
    public function getAccountAction(EntityManagerInterface $entityManager)
    {
        if ($user = $this->getUser()) {
            return $entityManager->getRepository(User::class)->find($user->getId());
        }

        return null;
    }
}
