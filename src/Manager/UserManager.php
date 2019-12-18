<?php

namespace App\Manager;

use App\Entity\Address;
use App\Entity\Phone;
use App\Entity\User;
use App\Form\RegisterType;
use App\Form\UserType;
use App\Traits\FormErrorFormatter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class UserManager
{
    use FormErrorFormatter;

    /** @var EntityManagerInterface $em */
    private $em;
    /** @var Connection $connection */
    private $connection;
    /** @var LoggerInterface */
    private $logger;
    /** @var TranslatorInterface */
    private $translator;
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;
    /** @var TokenGeneratorInterface */
    private $tokenGenerator;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $em,
        Connection $connection,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        FormFactoryInterface $formFactory,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->em = $em;
        $this->connection = $connection;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
        $this->tokenGenerator = $tokenGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Register Form User
     *
     * @param Request $request
     *
     * @return User|JsonResponse
     * @throws Exception
     */
    public function manageRegister(Request $request)
    {
        $user = $this->createUser();
        /** @var FormInterface $form */
        $form = $this->createRegisterForm($user);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    if ($searchUser = $this->getUserByEmail($user->getEmail())) {
                        throw new Exception("user.already.exists", 400);
                    }
                    $user->addRole('ROLE_USER');
                    if ($user->getAppMetadata()->getCoach() === true) {
                        $user->addRole('ROLE_MONO');
                    }
                    $token = $this->tokenGenerator->generateToken();
                    $user->setConfirmationToken($token);
                    $user->setSalt($token);

                    $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
                    $user->setEmail($user->getEmail());
                    $user->setUsername($user->getEmail());

                    $this->em->persist($user);
                    $this->em->flush();

                    return $user;
                } catch (Exception $e) {
                    $error = (new FormError($e->getMessage()));
                    $error->setOrigin($form->get('email'));
                    $form->addError($error);
                    return FormErrorFormatter::getErrorsAsJsonResponse($form);
                }
            }
        }
        return FormErrorFormatter::getErrorsAsJsonResponse($form);
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->em->getRepository(User::class)->getUsers();
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getUser(string $slug)
    {
        return $this->em->getRepository(User::class)->getUser($slug);
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    /**
     * @param $user
     *
     * @return mixed
     */
    private function createRegisterForm(User $user)
    {
        return $this->formFactory->create(
            RegisterType::class,
            $user
        );
    }

    /**
     * @param $user
     *
     * @return mixed
     */
    private function createUserForm($user)
    {
        return $this->formFactory->create(
            UserType::class,
            $user
        );
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws Exception
     */
    public function post(Request $request)
    {
        /** @var User $user */
        $user = $this->createUser();
        $form = $this->createUserForm($user);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();
                $user->setRoles(['ROLE_PART']);
                $user->setEnabled(true);
                $user->setPassword('');
                $this->em->persist($user);
                $this->em->flush();

                return $user;
            }
        }

        return $user;
    }

    /**
     * @param Request $request
     * @param         $email
     *
     * @return mixed
     */
    public function patch(Request $request, $email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        $form = $this->createUserForm($user);
        if ($request->isMethod('PATCH')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $user = $form->getData();

                    $this->updateUser($user);
                } catch (\Exception $e) {
                    $this->logger->debug(
                        $e->getMessage(),
                        array(
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'l.' => $e->getLine()
                        )
                    );
                }
            }
        }

        return $user;
    }

    /**
     * Create new user
     * @return User
     * @throws Exception
     */
    private function createUser(): User
    {
        $user = new User();
        $user->setExpired(false)
            ->setLocked(false)
            ->setCredentialsExpired(false)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $user;
    }

    public function getUserByToken(Request $request, $token)
    {
        return $this->em->getRepository('App:User')->findOneByConfirmationToken($token);
    }

    public function getUserByEmailType(Request $request, $email, $type)
    {
        return $this->em->getRepository('App:User')->findOneBy(array('email' => $email, 'type' => $type));
    }

    public function isValid(Request $request, $token)
    {
        $user = $this->em->getRepository('App:User')->findOneByConfirmationToken($token);

        $this->logger->debug(
            'user info',
            array(
                'user' => array(
                    $user->getNationality(),
                    $user->getAddress() instanceof Address ? $user->getAddress()->getCountry() : null,
                    $user->getBirthdate(),
                    $user->eml(),
                    $user->getLastName(),
                    $user->getFirstName(),
                )
            )
        );
        if ($user and $user instanceof User) {
            if (!empty($user->getNationality()) and
                (($user->getAddress() instanceof Address) and !empty($user->getAddress()->getCountry())) and
                !empty($user->getBirthdate()) and
                !empty($user->eml()) and
                !empty($user->getLastName()) and
                !empty($user->getFirstName())
            ) {
                return true;
            }
        }

        return false;
    }

    public function enable(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $this->logger->debug('data info', array(
            'data' => $data,
            'request' => $request->request->all()
        ));

        $user = $this->em->getRepository('App:User')->findOneByConfirmationToken($data['token']);
        if (!is_object($user)) {
            throw new \Exception("findOneByConfirmationToken(token) return null");
        }

        $user->setEnabled(true);
        $user->setMangopayId(isset($data['user']['mangopayId']) ? $data['user']['mangopayId'] : null);
        $user->setPlanningId(isset($data['user']['planning']) ? $data['user']['planning'] : null);
        $user->setPlanningToken(isset($data['user']['planningToken']) ? $data['user']['planningToken'] : null);

        $user->setFirstName(isset($data['user']['firstName']) ? $data['user']['firstName'] : null);
        $user->setLastName(isset($data['user']['lastName']) ? $data['user']['lastName'] : null);
        $user->setGender(isset($data['user']['gender']) ? $data['user']['gender'] : true);
        $user->setBirthdate(new \DateTime(isset($data['user']['birthdate']['date']) ? $data['user']['birthdate']['date'] : 'now'));
        $user->setNationality(isset($data['user']['nationality']) ? $data['user']['nationality'] : null);

        if (isset($data['user']['address'])) {
            if (!$user->getAddress() instanceof Address) {
                $address = new Address();
            } else {
                $address = $user->getAddress();
            }
            $address->setStreet(isset($data['user']['address']['street']) ? $data['user']['address']['street'] : null);
            $address->setPostalCode(isset($data['user']['address']['postalCode']) ? $data['user']['address']['postalCode'] : null);
            $address->setCity(isset($data['user']['address']['city']) ? $data['user']['address']['city'] : null);
            $address->setCountry(isset($data['user']['address']['country']) ? $data['user']['address']['country'] : '');
            $address->setUser($user);
            $user->setAddress($address);

            $this->em->persist($address);
        }

        if (isset($data['user']['phone']['number'])) {
            if (!$user->getPhone() instanceof Phone) {
                $phone = new Phone();
            } else {
                $phone = $user->getPhone();
            }
            $phone->setChecked(false);
            $phone->setNumber(isset($data['user']['phone']['number']) ? $data['user']['phone']['number'] : '');
            $phone->setCountryCode(isset($data['user']['phone']['countryCode']) ? $data['user']['phone']['countryCode'] : 'fr');
            $phone->setCountryNumber(isset($data['user']['phone']['countryNumber']) ? $data['user']['phone']['countryNumber'] : '');
            $phone->setUser($user);
            $user->setPhone($phone);

            $this->em->persist($phone);
        }
        $user->setConfirmationToken(null);

        $this->updateUser($user);

        return true;
    }
}
