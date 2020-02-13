<?php

namespace App\Controller\Front;

use App\Entity\Sport;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class CoachController extends AbstractFOSRestController
{
    /**
     * List of all coachs
     * @param Request $request
     *
     * @param UserManager $userManager
     *
     * @param EntityManagerInterface $entityManager
     * @param $slug
     * @return array
     * @Annotations\View(serializerGroups={"getUsers"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users", name="users")
     * @Annotations\Get("/users/sports/{slug}", name="users_sport")
     */
    public function getUsersAction(
        Request $request,
        UserManager $userManager,
        EntityManagerInterface $entityManager,
        $slug = null
    ) {
        if ($slug) {
            /** @var Sport $sport */
            $sport = $entityManager->getRepository(Sport::class)
                ->findOneBy(['slug' => $slug]);

            return $userManager->getUsers($sport, $request->query->all());
        }

        return $userManager->getUsers(null, $request->query->all());
    }

    /**
     * Find a coach by slug
     *
     * @Annotations\View(serializerGroups={"getUser"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users/{slug}")
     * @param             $slug
     * @param UserManager $userManager
     *
     * @return User[]
     */
    public function getCoachByIdAction($slug, UserManager $userManager)
    {
        return $userManager->getUser($slug);
    }
}
