<?php

namespace App\Controller\Front;

use App\Entity\Address;
use App\Entity\Phone;
use App\Entity\User;
use App\Manager\UserManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByEmail"})
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
     * @Annotations\View(serializerGroups={"registerUser"})
     * @Annotations\Post("/users/register")
     * @param Request     $request
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
     * @Annotations\View(serializerGroups={"Default", "getIsValidUser"})
     * @Annotations\Get("/users/valid/{token}")
     */
    public function getIsValidUserAction(Request $request, $token, UserManager $userManager)
    {
        return $userManager->isValid($request, $token);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByToken"})
     * @Annotations\Get("/users/token/{token}")
     */
    public function getUserByTokenAction(Request $request, $token, UserManager $userManager)
    {
        return $userManager->getUserByToken($request, $token);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByEmailType"})
     * @Annotations\Get("/users/email/{email}/type/{type}")
     */
    public function getUserByUsernameTypeAction(Request $request, $email, $type, UserManager $userManager)
    {
        return $userManager->getUserByEmailType($request, $email, $type);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postEnable"})
     * @Annotations\Post("/users/enable")
     */
    public function postEnableAction(Request $request, UserManager $userManager)
    {
        return $userManager->enable($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postConfirm"})
     * @Annotations\Post("/register/confirm")
     */
    public function postConfirmAction(Request $request, UserManager $userManager)
    {
        $params = json_decode($request->getContent(), true);
        $user = $this->getDoctrine()->getRepository('App:User')->findOneByConfirmationToken($params['token']);

        if (is_object($user)) {
            if ($params['step'] == 1) {
                return $user->getEmail();
            }

            try {
                if ($user->isEnabled() == 1) {
                    return false;
                }

                $userData = $params['user'];

                $user->setFirstName($userData['firstName']);
                $user->setLastName($userData['lastName']);
                $user->setGender($userData['gender']);
                $user->setBirthdate(new \DateTime($userData['birthdate']['date']));
                $user->setNationality($userData['nationality']);

                $address = new Address;
                $address->setUser($user);
                $user->setAddress($address);
                $user->getAddress()->setStreet($userData['address']['street']);
                $user->getAddress()->setPostalCode($userData['address']['postalCode']);
                $user->getAddress()->setCity($userData['address']['city']);
                $user->getAddress()->setCountry($userData['address']['country']);

                $phone = new Phone;
                $phone->setUser($user);
                $user->setPhone($phone);
                $user->getPhone()->setNumber($userData['phone']['number']);
                $user->getPhone()->setCountryCode($userData['phone']['countryCode']);
                $user->getPhone()->setCountryNumber($userData['phone']['countryNumber']);

                $user->setMangopayId($userData['mangopayId']);
                $user->setPlanningId($userData['planning']);
                $user->setPlanningToken($userData['planningToken']);

                $user->setEnabled(true);

                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($user);

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postPassword"})
     * @Annotations\Post("/register/password")
     */
    public function postPasswordAction(Request $request, UserManager $userManager)
    {

        $data = json_decode($request->getContent(), true);
        $email = $data['email'];

        $user = $this->getDoctrine()->getRepository('App:User')->findOneBy(
            array('email' => $email, "type" => "zeemono")
        );

        if (is_object($user)) {
            if (!$user->isEnabled()) {
                return 'NE';
            }

            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            return $user->getConfirmationToken();
        }
        return false;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postResetPassword"})
     * @Annotations\Post("/register/reset/password")
     */
    public function postResetPasswordAction(UserManager $userManager)
    {

        $data = json_decode($this->get("request")->getContent(), true);
        $user = $this->getDoctrine()->getRepository('App:User')->findOneByConfirmationToken($data['token']);

        if (is_object($user)) {
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword($data['password'], $user->getSalt()));
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());

            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            return true;
        }
        return false;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postUsersAction"})
     * @Annotations\Post("/users")
     */
    public function postUsersAction(Request $request, UserManager $userManager)
    {
        return $userManager->post($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchUsersAction"})
     * @Annotations\Patch("/users/{email}")
     */
    public function patchUsersAction(Request $request, $email, UserManager $userManager)
    {
        return $userManager->patch($request, $email);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postSocial"})
     * @Annotations\Post("/login/social")
     */
    public function postLoginSocialAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository('App:User')->findOneBy(
            array(
                'accessToken' => $data['_accessToken'],
                'username' => $data['_username']
            )
        );
        if ($user instanceof User) {
            $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);
            return new JsonResponse(['token' => $token]);
        }
        return false;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postLoginFacebook"})
     * @Annotations\Post("/login/facebook")
     */
    public function postLoginFacebookAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository('App:User')->findOneByFacebookId($data['_password']);
        if (is_object($user)) {
            $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);
            return new JsonResponse(['token' => $token]);
        }
        return false;
    }
}
