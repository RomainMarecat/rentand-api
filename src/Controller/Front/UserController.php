<?php

namespace App\Controller\Front;

use Entity\Address;
use Entity\Phone;
use Entity\User;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByEmail"})
     * @Annotations\Get("/users/{email}/email")
     */
    public function getUserByEmailAction($email)
    {
        return $this->get('manager.user')->getUserByEmail($email);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postRegisterUsers"})
     * @Annotations\Post("/users/register")
     */
    public function postRegisterUsersAction(Request $request)
    {
        list($user, $form) = $this->get('manager.user')->manageRegister($request);

        return array(
            'user' => $user,
            'form' => $this->get('services.form_parser')->parseErrors($form),
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getIsValidUser"})
     * @Annotations\Get("/users/valid/{token}")
     */
    public function getIsValidUserAction(Request $request, $token)
    {
        return $this->get('manager.user')->isValid($request, $token);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByToken"})
     * @Annotations\Get("/users/token/{token}")
     */
    public function getUserByTokenAction(Request $request, $token)
    {
        return $this->get('manager.user')->getUserByToken($request, $token);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByEmailType"})
     * @Annotations\Get("/users/username/{username}/type/{type}")
     */
    public function getUserByUsernameTypeAction(Request $request, $username, $type)
    {
        return $this->get('manager.user')->getUserByUsernameType($request, $username, $type);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postEnable"})
     * @Annotations\Post("/users/enable")
     */
    public function postEnableAction(Request $request)
    {
        return $this->get('manager.user')->enable($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postConfirm"})
     * @Annotations\Post("/register/confirm")
     */
    public function postConfirmAction(Request $request)
    {
        $params = json_decode($request->getContent(), true);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByConfirmationToken($params['token']);

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
    public function postPasswordAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $email = $data['email'];

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(
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
    public function postResetPasswordAction()
    {

        $data = json_decode($this->get("request")->getContent(), true);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByConfirmationToken($data['token']);

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
    public function postUsersAction(Request $request)
    {
        return $this->get('manager.user')->post($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchUsersAction"})
     * @Annotations\Patch("/users/{email}")
     */
    public function patchUsersAction(Request $request, $email)
    {
        return $this->get('manager.user')->patch($request, $email);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postSocial"})
     * @Annotations\Post("/login/social")
     */
    public function postLoginSocialAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(
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

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByFacebookId($data['_password']);
        if (is_object($user)) {
            $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);
            return new JsonResponse(['token' => $token]);
        }
        return false;
    }

    // /**
    //  * @Annotations\View(serializerGroups={"Default", "isFacebook"})
    //  * @Annotations\Get("/has/facebook")
    //  */
    // public function isFacebookAction($id)
    // {

    //     $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByFacebookId($id);
    //     if (is_object($user)) {
    //         return true;
    //     }
    //     return false;
    // }
}
