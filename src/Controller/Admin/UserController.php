<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\Phone;
use App\Entity\User;
use App\Manager\Admin\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\FOSRestController;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Prefix("admin")
 * @NamePrefix("_admin")
 */
class UserController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"adminGetUsers"})
     * @Annotations\Get("/users")
     * @param Request $request , UserManager $userManager
     * @param UserManager $userManager
     * @return
     */
    public function getUsersAction(Request $request, UserManager $userManager)
    {
        return $userManager->adminCget();
    }

    /**
     * @Annotations\View(serializerGroups={"adminGetUsersErrors"})
     * @Annotations\Get("/users_errors")
     * @param Request $request
     * @param UserManager $userManager
     * @return mixed
     */
    public function getUsersErrorsAction(Request $request, UserManager $userManager)
    {
        return $userManager->adminCgetErrors();
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUsersByMangopayId"})
     * @Annotations\Get("/users/mangopayid/{mangopayId}")
     * @Annotations\Get("/users/mangopayid/", name="_mangopayid_null")
     * @param Request $request
     * @param UserManager $userManager
     * @param $mangopayId
     * @return mixed
     */
    public function getUsersByMangopayIdAction(Request $request, UserManager $userManager, $mangopayId)
    {
        return $userManager->getUsersByMangopayId($mangopayId);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postUsersByMangopayId"})
     * @Annotations\Post("/users/mangopayid")
     * @param Request $request
     * @param UserManager $userManager
     * @return mixed
     */
    public function postUsersByMangopayIdAction(Request $request, UserManager $userManager)
    {
        return $userManager->postUsersByMangopayId($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUsersMonos"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Paginator $paginator
     * @return array|\Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getUsersMonosAction(
        Request $request,
        EntityManagerInterface $entityManager,
        Paginator $paginator
    ) {

        if ($request->query->get('sort')) {
            $sort = explode(":", $request->query->get('sort'));
            $sortParams = array('id', 'email', 'enabled', 'lastLogin', 'role', 'firstName', 'lastName', 'gender', 'birthdate', 'nationality', 'newsletter', 'createdAt', 'updatedAt');
            if (($sort[1] != 'asc' && $sort[1] != 'desc') || (!in_array($sort[0], $sortParams))) {
                $query = $entityManager
                    ->createQuery('SELECT u FROM App:User u WHERE u.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_MONO"%');
                $users = $query->getResult();
            } else {
                $query = $entityManager
                    ->createQuery('SELECT u FROM App:User u WHERE u.roles LIKE :role ORDER BY u.' . $sort[0] . ' ' . $sort[1])
                    ->setParameter('role', '%"ROLE_MONO"%');
                $users = $query->getResult();
            }
        } else {
            $query = $entityManager
                ->createQuery('SELECT u FROM App:User u WHERE u.roles LIKE :role')
                ->setParameter('role', '%"ROLE_MONO"%');
            $users = $query->getResult();
        }

        if (!is_array($users)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                return $paginator->paginate($users, $page, $perPage);
            } else {
                return $users;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUsersParticuliers"})
     * @param Request $request
     * @param UserManager $userManager
     * @param EntityManagerInterface $entityManager
     * @param Paginator $paginator
     * @return array|\Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getUsersParticuliersAction(Request $request,
                                               UserManager $userManager,
                                               EntityManagerInterface $entityManager,
                                               Paginator $paginator
    ) {

        if ($request->query->get('sort')) {
            $sort = explode(":", $request->query->get('sort'));
            $sortParams = array('id', 'email', 'enabled', 'lastLogin', 'role', 'firstName', 'lastName', 'gender', 'birthdate', 'nationality', 'newsletter', 'createdAt', 'updatedAt');
            if (($sort[1] != 'asc' && $sort[1] != 'desc') || (!in_array($sort[0], $sortParams))) {
                $query = $entityManager
                    ->createQuery('SELECT u FROM App:User u WHERE u.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_PART"%');
                $users = $query->getResult();
            } else {
                $query = $entityManager
                    ->createQuery('SELECT u FROM App:User u WHERE u.roles LIKE :role ORDER BY u.' . $sort[0] . ' ' . $sort[1])
                    ->setParameter('role', '%"ROLE_PART"%');
                $users = $query->getResult();
            }
        } else {
            $query = $entityManager
                ->createQuery('SELECT u FROM App:User u WHERE u.roles LIKE :role')
                ->setParameter('role', '%"ROLE_PART"%');
            $users = $query->getResult();
        }

        if (!is_array($users)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                return $paginator->paginate($users, $page, $perPage);
            } else {
                return $users;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "adminGetUser"})
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return object|null
     */
    public function getUserAction($id, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository('App:User')->find($id);
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        } else {
            return $user;
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postUser"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param TokenGeneratorInterface $tokenGenerator
     * @return string
     * @throws \Exception
     */
    public function postUserAction(Request $request,
                                   EntityManagerInterface $entityManager,
                                   ValidatorInterface $validator,
                                   TokenGeneratorInterface $tokenGenerator
    ) {

        $params = json_decode($request->getContent(), true);

        // check email
        $searchEmail = $entityManager->getRepository('App:User')
            ->findOneBy(
                array('email' => $params['email'], "type" => "zeemono")
            );
        if (is_object($searchEmail)) {
            return 'EMAIL';
        }

        // init user
        $user = new User();
        $user->setEmail($params['email']);
        $user->setUsername($params['email']);
        $user->setPlanningId($params['planningId']);
        $user->setPlanningToken($params['planningToken']);
        $user->setMangopayId($params['mangopayId']);

        $address = new Address();
        $address->setUser($user);
        $user->setAddress($address);
        $phone = new Phone();
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
        $token = substr($tokenGenerator->generateToken(), 0, 12);
        $user->setConfirmationToken($token);
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($params['password'], $user->getSalt()));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid user: ' . json_encode($errors));
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteUser"})
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return string
     */
    public function deleteUserAction(
        $id,
        EntityManagerInterface $entityManager
    ) {

        $user = $entityManager->getRepository('App:User')->find($id);
        if (!is_object($user)) {
            throw $this->createNotFoundException();
        } else {
            $entityManager->remove($user);
            $entityManager->flush();

            return "USER DELETED";
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchUser"})
     * @param Request $request
     * @param UserManager $userManager
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param $id
     * @return object|null
     * @throws \Exception
     */
    public function patchUserAction(Request $request,
                                    UserManager $userManager,
                                    ValidatorInterface $validator,
                                    EntityManagerInterface $entityManager,
                                    TokenGeneratorInterface $tokenGenerator,
                                    $id
    ) {

        $user = $entityManager->getRepository('App:User')->find($id);

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
        $em = $entityManager;
        if (isset($params['address'])) {
            if ($user->getAddress() == null) {
                $address = new Address();
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
                $phone = new Phone();
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
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(400, 'Invalid user: ' . json_encode($errors));
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"adminGetUsers"})
     * @Annotations\Get("/users/planning/{id}")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return array
     */
    public function getUsersByPlanningAction(
        EntityManagerInterface $entityManager,
        $id
    ) {
        return array('user' => $entityManager->getRepository('App:User')->findOneByPlanningId($id));
    }
}
