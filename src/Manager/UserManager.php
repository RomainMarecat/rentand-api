<?php

namespace App\Manager;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\UserType;
use App\Traits\FormErrorFormatter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
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
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class UserManager
{
    use FormErrorFormatter;

    /** @var JWTTokenManagerInterface $jwtTokenManager */
    private $jwtTokenManager;
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
        JWTTokenManagerInterface $jwtTokenManager,
        EntityManagerInterface $em,
        Connection $connection,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        FormFactoryInterface $formFactory,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->jwtTokenManager = $jwtTokenManager;
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
            $data = $request->request->get($form->getName());
            if (isset($data['user_metadata'])) {
                $data['userMetadata'] = $data['user_metadata'];
                unset($data['user_metadata']);
            }

            $form->submit($data);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    if ($searchUser = $this->getUserByEmail($user->getEmail())) {
                        throw new Exception("The user already exists", 400);
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

                    $jwt = $this->jwtTokenManager->create($user);
                    $response = new JsonResponse();
                    $event = new AuthenticationSuccessEvent(array('token' => $jwt), $user, $response);
                    $response->setData($event->getData());

                    return $response;
                } catch (Exception $e) {
                    $error = (new FormError($e->getMessage()));
                    $form->get('email')->addError($error);
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
     * @param         $username
     *
     * @return mixed
     */
    public function update(Request $request, string $username)
    {
        $data = $request->request->all();
        if (isset($data['user_metadata'])) {
            $data['userMetadata'] = $data['user_metadata'];
            unset($data['user_metadata']);

            if (isset($data['userMetadata']['mother_lang'])) {
                $data['userMetadata']['motherLang'] = $data['userMetadata']['mother_lang']['id'];
                unset($data['userMetadata']['mother_lang']);
            }
            if (isset($data['userMetadata']['address']['country']['id'])) {
                $data['userMetadata']['address']['country'] = $data['userMetadata']['address']['country']['id'];
            }
            if (isset($data['userMetadata']['nationality']['id'])) {
                $data['userMetadata']['nationality'] = $data['userMetadata']['nationality']['id'];
            }
            if (isset($data['userMetadata']['phone']['country_code'])) {
                $data['userMetadata']['phone']['countryCode'] = $data['userMetadata']['phone']['country_code'];
            }
            if (isset($data['userMetadata']['phone']['country_number'])) {
                $data['userMetadata']['phone']['countryNumber'] = $data['userMetadata']['phone']['country_number'];
            }
        }


        $user = $this->em->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        $form = $this->createUserForm($user);
        if ($request->isMethod('PATCH')) {
            $form->submit($data, false);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();

                return $this->updateUser($user);
            }
        }

        return FormErrorFormatter::getErrorsAsJsonResponse($form);
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

    /**
     * Update current user informations
     *
     * @param User $user
     *
     * @return User
     */
    private function updateUser(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param $token
     * @return object|null
     */
    public function getUserByToken($token)
    {
        return $this->em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);
    }

    /**
     * @param Request $request
     * @param $email
     * @param $type
     * @return object|null
     */
    public function getUserByEmailType(Request $request, $email, $type)
    {
        return $this->em->getRepository(User::class)->findOneBy(array('email' => $email, 'type' => $type));
    }
}
