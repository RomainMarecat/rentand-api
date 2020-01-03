<?php

namespace App\Manager;

use App\Entity\Address;
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
     * @param         $username
     *
     * @return mixed
     */
    public function patch(Request $request, string $username)
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

        $this->logger->debug(
            'debug form',
            [
                'valid' => $form->isValid(),
                'data' => $form->getData(),
                'extra_data' => $form->getExtraData(),
            ]
        );

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

    public function getUserByToken(Request $request, $token)
    {
        return $this->em->getRepository(User::class)->findOneByConfirmationToken($token);
    }

    public function getUserByEmailType(Request $request, $email, $type)
    {
        return $this->em->getRepository(User::class)->findOneBy(array('email' => $email, 'type' => $type));
    }

    public function isValid(Request $request, $token)
    {
        $user = $this->em->getRepository(User::class)->findOneByConfirmationToken($token);

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
}
