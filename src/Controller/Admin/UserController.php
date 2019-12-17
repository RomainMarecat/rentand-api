<?php

namespace App\Controller\Admin;

use Entity\Address;
use Entity\Phone;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Prefix("admin")
 * @NamePrefix("_admin")
 */
class UserController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"adminGetUsers"})
     * @Annotations\Get("/users")
     */
    public function getUsersAction(Request $request)
    {
        return $this->get('admin.manager.user')->adminCget();
    }

    /**
     * @Annotations\View(serializerGroups={"adminGetUsersErrors"})
     * @Annotations\Get("/users_errors")
     */
    public function getUsersErrorsAction(Request $request)
    {
        return $this->get('admin.manager.user')->adminCgetErrors();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUsersByMangopayId"})
     * @Annotations\Get("/users/mangopayid/{mangopayId}")
     * @Annotations\Get("/users/mangopayid/", name="_mangopayid_null")
     */
    public function getUsersByMangopayIdAction(Request $request, $mangopayId)
    {
        return $this->get('admin.manager.user')->getUsersByMangopayId($mangopayId);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postUsersByMangopayId"})
     * @Annotations\Post("/users/mangopayid")
     */
    public function postUsersByMangopayIdAction(Request $request)
    {
        return $this->get('admin.manager.user')->postUsersByMangopayId($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUsersMonos"})
     */
    public function getUsersMonosAction(Request $request)
    {

        if ($request->query->get('sort')) {
            $sort = explode(":", $request->query->get('sort'));
            $sortParams = array('id', 'email', 'enabled', 'lastLogin', 'role', 'firstName', 'lastName', 'gender', 'birthdate', 'nationality', 'newsletter', 'createdAt', 'updatedAt');
            if (($sort[1] != 'asc' && $sort[1] != 'desc') || (!in_array($sort[0], $sortParams))) {
                $query = $this->getDoctrine()->getManager()
                    ->createQuery('SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_MONO"%');
                $users = $query->getResult();
            } else {
                $query = $this->getDoctrine()->getManager()
                    ->createQuery('SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role ORDER BY u.' . $sort[0] . ' ' . $sort[1])
                    ->setParameter('role', '%"ROLE_MONO"%');
                $users = $query->getResult();
            }
        } else {
            $query = $this->getDoctrine()->getManager()
                ->createQuery('SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role')
                ->setParameter('role', '%"ROLE_MONO"%');
            $users = $query->getResult();
        }

        if (!is_array($users)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                $paginator = $this->get('knp_paginator');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                $results = $paginator->paginate($users, $page, $perPage);
                return $results;
            } else {
                return $users;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUsersParticuliers"})
     */
    public function getUsersParticuliersAction(Request $request)
    {

        if ($request->query->get('sort')) {
            $sort = explode(":", $request->query->get('sort'));
            $sortParams = array('id', 'email', 'enabled', 'lastLogin', 'role', 'firstName', 'lastName', 'gender', 'birthdate', 'nationality', 'newsletter', 'createdAt', 'updatedAt');
            if (($sort[1] != 'asc' && $sort[1] != 'desc') || (!in_array($sort[0], $sortParams))) {
                $query = $this->getDoctrine()->getManager()
                    ->createQuery('SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_PART"%');
                $users = $query->getResult();
            } else {
                $query = $this->getDoctrine()->getManager()
                    ->createQuery('SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role ORDER BY u.' . $sort[0] . ' ' . $sort[1])
                    ->setParameter('role', '%"ROLE_PART"%');
                $users = $query->getResult();
            }
        } else {
            $query = $this->getDoctrine()->getManager()
                ->createQuery('SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role')
                ->setParameter('role', '%"ROLE_PART"%');
            $users = $query->getResult();
        }

        if (!is_array($users)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                $paginator = $this->get('knp_paginator');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                $results = $paginator->paginate($users, $page, $perPage);
                return $results;
            } else {
                return $users;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "adminGetUser"})
     */
    public function getUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        } else {
            return $user;
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postUser"})
     */
    public function postUserAction(Request $request)
    {

        $params = json_decode($request->getContent(), true);

        // check email
        $searchEmail = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findOneBy(
                array('email' => $params['email'], "type" => "zeemono")
            );
        if (is_object($searchEmail)) {
            return 'EMAIL';
        }

        $validator = $this->get('validator');
        // init user
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEmail($params['email']);
        $user->setUsername($params['email']);
        $user->setPlanningId($params['planningId']);
        $user->setPlanningToken($params['planningToken']);
        $user->setMangopayId($params['mangopayId']);

        $address = new address;
        $address->setUser($user);
        $user->setAddress($address);
        $phone = new phone;
        $phone->setUser($user);
        $user->setPhone($phone);

        $user->setFirstName($params['firstName']);
        $user->setLastName($params['lastName']);
        $user->setGender($params['gender']);
        $user->setBirthdate(new \DateTime($params['birthdate']['date']));
        $user->setNationality($params['nationality']);
        $user->setNewsletter($params['newsletter']);
        if (isset($params['address'])) {
            $user->getAddress()->setStreet($params['address']['street']);
            $user->getAddress()->setPostalCode($params['address']['postalCode']);
            $user->getAddress()->setCity($params['address']['city']);
            $user->getAddress()->setCountry($params['address']['country']);
        }
        if (isset($params['phone'])) {
            $user->getPhone()->setNumber($params['phone']['number']);
            $user->getPhone()->setCountryCode($params['phone']['countryCode']);
            $user->getPhone()->setCountryNumber($params['phone']['countryNumber']);
            $user->getPhone()->setChecked($params['phone']['checked']);
        }
        if (isset($params['enabled'])) {
            $user->setEnabled($params['enabled']);
        }
        if (isset($params['adminComment'])) {
            $user->setAdminComment($params['adminComment']);
        }

        if ($params['type'] == 2) {
            $user->addRole("ROLE_MONO");
        } elseif ($params['type'] == 3) {
            $user->addRole("ROLE_STRU");
        } else {
            $user->addRole("ROLE_PART");
        }
        if (isset($params['admin'])) {
            if ((gettype($params['admin']) == "boolean") && ($params['admin'] == true)) {
                $user->addRole("ROLE_ADMIN");
            }
        }

        // create password
        $tokenGenerator = $this->container->get('fos_user.util.token_generator');
        $token = substr($tokenGenerator->generateToken(), 0, 12);
        $user->setConfirmationToken($token);
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($params['password'], $user->getSalt()));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid user: ' . $errors);
        }

        $userManager->updateUser($user);

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteUser"})
     */
    public function deleteUserAction($id)
    {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return "USER DELETED";
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchUser"})
     */
    public function patchUserAction(Request $request, $id)
    {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if (!is_object($user)) {
            throw $this->createNotFoundException();
        }

        $params = json_decode($request->getContent(), true);

        if (isset($params['email'])) {
            $user->setFirstName($params['email']);
        }
        if (isset($params['firstName'])) {
            $user->setFirstName($params['firstName']);
        }
        if (isset($params['lastName'])) {
            $user->setLastName($params['lastName']);
        }
        if (isset($params['gender'])) {
            $user->setGender($params['gender']);
        }
        if (isset($params['birthdate'])) {
            $user->setBirthdate(new \DateTime($params['birthdate']['date']));
        }
        if (isset($params['nationality'])) {
            $user->setNationality($params['nationality']);
        }
        if (isset($params['newsletter'])) {
            $user->setNewsletter($params['newsletter']);
        }
        if (isset($params['adminComment'])) {
            $user->setAdminComment($params['adminComment']);
        }
        $em = $this->getDoctrine()->getManager();
        if (isset($params['address'])) {
            if ($user->getAddress() == null) {
                $address = new address;
                $address->setUser($user);
                $user->setAddress($address);
            }
            $user->getAddress()->setStreet($params['address']['street']);
            $user->getAddress()->setPostalCode($params['address']['postalCode']);
            $user->getAddress()->setCity($params['address']['city']);
            $user->getAddress()->setCountry($params['address']['country']);
            if (isset($address)) {
                $em->persist($address);
            }
        }
        if (isset($params['phone'])) {
            if ($user->getPhone() == null) {
                $phone = new phone;
                $phone->setUser($user);
                $user->setPhone($phone);
            }
            $user->getPhone()->setNumber($params['phone']['number']);
            $user->getPhone()->setCountryCode($params['phone']['countryCode']);
            $user->getPhone()->setCountryNumber($params['phone']['countryNumber']);
            $user->getPhone()->setChecked($params['phone']['checked']);
            if (isset($phone)) {
                $em->persist($phone);
            }
        }
        if (isset($params['enabled'])) {
            $user->setEnabled($params['enabled']);
        }
        if (isset($params['mangopayId'])) {
            $user->setMangopayId($params['mangopayId']);
        }
        if (isset($params['planningId'])) {
            $user->setPlanningId($params['planningId']);
        }
        if (isset($params['planningToken'])) {
            $user->setPlanningToken($params['planningToken']);
        }
        if (isset($params['password'])) {
            // create new password
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $token = substr($tokenGenerator->generateToken(), 0, 12);
            $user->setConfirmationToken($token);
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword($params['password'], $user->getSalt()));
        }
        if (isset($params['admin'])) {
            if ($params['admin']) {
                $user->addRole("ROLE_ADMIN");
            } else {
                $user->removeRole("ROLE_ADMIN");
            }
        }

        $validator = $this->get('validator');
        $userManager = $this->get('fos_user.user_manager');

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid user: ' . $errors);
        }

        $userManager->updateUser($user);

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"adminGetUsers"})
     * @Annotations\Get("/users/planning/{id}")
     */
    public function getUsersByPlanningAction(Request $request, $id)
    {
        return array('user' => $this->getDoctrine()->getRepository('AppBundle:User')->findOneByPlanningId($id));
    }
}
