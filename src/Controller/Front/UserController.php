<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Manager\UserManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractFOSRestController
{
    /**
     * Find a user by email
     *
     * @Annotations\View(serializerGroups={"getUserByEmail"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users/{email}/email")
     * @param             $email
     * @param UserManager $userManager
     *
     * @return mixed
     */
    public function getUserByEmailAction($email, UserManager $userManager)
    {
        return $userManager->getUserByEmail($email);
    }

    /**
     * Register new user
     *
     * @Annotations\View(serializerGroups={"registerUser"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Post("/users/register")
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return User|JsonResponse
     * @throws \Exception
     */
    public function postRegisterUsersAction(Request $request, UserManager $userManager)
    {
        return $userManager->manageRegister($request);
    }

    /**
     * Find user by confirmationToken
     *
     * @Annotations\View(serializerGroups={"getUserByToken"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users/token/{token}")
     * @param $token
     * @param UserManager $userManager
     * @return object|null
     */
    public function getUserByTokenAction($token, UserManager $userManager)
    {
        return $userManager->getUserByToken($token);
    }

    /**
     * Find a user by email and type
     *
     * @Annotations\View(serializerGroups={"getUserByEmailType"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users/email/{email}/type/{type}")
     * @param $email
     * @param $type
     * @param UserManager $userManager
     * @return object|null
     */
    public function getUserByUsernameTypeAction($email, $type, UserManager $userManager)
    {
        return $userManager->getUserByEmailType($email, $type);
    }

    /**
     * Find user by email and update its data
     *
     * @Annotations\View(serializerGroups={"patchUsersAction"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Patch("/users/{email}")
     * @param Request $request
     * @param $email
     * @param UserManager $userManager
     * @return mixed
     */
    public function patchUsersAction(Request $request, $email, UserManager $userManager)
    {
        return $userManager->update($request, $email);
    }
}
