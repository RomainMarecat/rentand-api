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
     * @return array
     * @todo need refacto by keywords search)
     *
     * @Annotations\View(serializerGroups={"getUsers"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users")
     */
    public function getUsersAction(Request $request, UserManager $userManager)
    {
        return $userManager->getUsers();
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

    /**
     * Filters user by sport
     *
     * @Annotations\Get("/users/sports/{slug}")
     * @Annotations\View(serializerGroups={"getUsers"}, serializerEnableMaxDepthChecks=true)
     * @param $slug
     *
     * @param EntityManagerInterface $entityManager
     * @return array
     */
    public function getUsersBySportAction($slug, EntityManagerInterface $entityManager)
    {
        /** @var Sport $sport */
        $sport = $entityManager->getRepository(Sport::class)->findOneBy(['slug' => $slug]);

        return $entityManager
            ->getRepository(User::class)
            ->getUsers($sport);
    }
}
