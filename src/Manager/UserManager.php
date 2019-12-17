<?php

namespace App\Manager;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Entity\Address;
use Entity\Phone;
use Entity\User;
use Form\RegisterType;
use Form\UserType;
use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * Class UserManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class UserManager
{
    protected $em;

    protected $connection;

    protected $fosUserManager;

    protected $logger;

    protected $translator;

    protected $formFactory;

    protected $tokenGenerator;

    protected $encoderFactory;

    protected $formParser;

    public function getUserByEmail($email)
    {
        return $this->getEm()->getRepository('AppBundle:User')->findOneByEmail($email);
    }

    public function getUserInformations($planning)
    {
        try {
            $informations = $this->getEm()
                ->getRepository('AppBundle:User')
                ->findPartialOneByPlanning($planning);

            if (empty($informations)) {
                throw new \Exception("plannings.user.informations.empty", 404);
            }
        } catch (\Exception $e) {
            $this->getLogger()->error(
                $e->getMessage(),
                array(
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'l.' => $e->getLine()
                )
            );

            throw $e;
        }

        return $informations;
    }

    protected function createRegisterForm($user)
    {
        $form = $this->getFormFactory()->create(
            RegisterType::class,
            $user
        );

        return $form;
    }

    protected function createUserForm($user)
    {
        $form = $this->getFormFactory()->create(
            UserType::class,
            $user
        );

        return $form;
    }

    public function post(Request $request)
    {
        $this->getEm()->getConnection()->beginTransaction();
        try {
            $user = $this->getFosUserManager()->createUser();
            $form = $this->createUserForm($user);
            if ($request->isMethod('POST')) {
                $form->submit($request->request->get($form->getName()));
                if ($form->isSubmitted() && $form->isValid()) {
                    try {
                        $user = $form->getData();
                        return $user;
                        $user->addRole('ROLE_PART');
                        $user->setEnabled(true);
                        $user->setPassword('');
                        $this->getFosUserManager()->updateUser($user);
                        $this->getEm()->getConnection()->commit();
                    } catch (\Exception $e) {
                        $this->getLogger()->error(
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
            $this->getLogger()->debug(
                "request debug",
                array(
                    'request' => $request->request->all(),
                    'form name' => $form->getName(),
                    'form method' => $request->getMethod(),
                    'user' => $user,
                    'data' => $form->getData(),
                    'extra data' => $form->getExtraData(),
                    'is valid' => $form->isValid(),
                    'is submitted' => $form->isSubmitted(),
                    'form errors' => (string)$form->getErrors(true, true),
                    'form parser errors' => $this->getFormParser()->parseErrors($form),
                )
            );
        } catch (\Exception $e) {
            $this->getLogger()->error(
                $e->getMessage(),
                array(
                    'l.' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                )
            );
        }

        return $user;
    }

    public function patch(Request $request, $username)
    {
        $user = $this->getFosUserManager()->loadByUsername($username);
        $form = $this->createUserForm($user);
        if ($request->isMethod('PATCH')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $user = $form->getData();

                    $this->getFosUserManager()->updateUser($user);
                } catch (\Exception $e) {
                    $this->getLogger()->debug(
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

    public function getUserByToken(Request $request, $token)
    {
        return $this->getEm()->getRepository('AppBundle:User')->findOneByConfirmationToken($token);
    }

    public function getUserByUsernameType(Request $request, $username, $type)
    {
        return $this->getEm()->getRepository('AppBundle:User')->findOneBy(array('username' => $username, 'type' => $type));
    }

    public function isValid(Request $request, $token)
    {
        $user = $this->getEm()->getRepository('AppBundle:User')->findOneByConfirmationToken($token);

        $this->getLogger()->debug(
            'user info',
            array(
                'user' => array(
                    $user->getNationality(),
                    $user->getAddress() instanceof Address ? $user->getAddress()->getCountry() : null,
                    $user->getBirthdate(),
                    $user->getEmail(),
                    $user->getLastName(),
                    $user->getFirstName(),
                )
            )
        );
        if ($user and $user instanceof User) {
            if (!empty($user->getNationality()) and
                (($user->getAddress() instanceof Address) and !empty($user->getAddress()->getCountry())) and
                !empty($user->getBirthdate()) and
                !empty($user->getEmail()) and
                !empty($user->getLastName()) and
                !empty($user->getFirstName())
            ) {
                return true;
            }
        }

        return false;
    }

    protected function addExtraData($extraData, User $user)
    {
        $this->getLogger()->debug(
            'extraData',
            array('extraData' => $extraData)
        );
        if (isset($extraData['country']) and is_string($extraData['country'])) {
            if (!$user->getAddress() instanceof Address) {
                $address = new Address();
            } else {
                $address = $user->getAddress();
            }

            $address->setCountry($extraData['country']);
            $address->setUser($user);
            $user->setAddress($address);

            $this->getEm()->persist($address);
        }

        if (isset($extraData['phone'])
            and is_array($extraData['phone'])
            and isset($extraData['phone']['countryCode'])
            and isset($extraData['phone']['countryNumber'])
            and isset($extraData['phone']['number'])
        ) {
            if (!$user->getPhone() instanceof Phone) {
                $phone = new Phone();
            } else {
                $phone = $user->getPhone();
            }
            $phone->setCountryCode($extraData['phone']['countryCode']);
            $phone->setCountryNumber($extraData['phone']['countryNumber']);
            $phone->setNumber($extraData['phone']['number']);
            $phone->setChecked(false);

            $phone->setUser($user);
            $user->setPhone($phone);

            $this->getEm()->persist($phone);
        }

        if (isset($extraData['firstName']) and is_string($extraData['country'])) {
            $user->setFirstName($extraData['firstName']);
        }

        if (isset($extraData['lastName']) and is_string($extraData['country'])) {
            $user->setLastName($extraData['lastName']);
        }

        if (isset($extraData['nationality']) and is_string($extraData['country'])) {
            $user->setNationality($extraData['nationality']);
        }

        if (isset($extraData['birthdate'])) {
            $d = \DateTime::createFromFormat('Y-m-d H:i:s', $extraData['birthdate']);
            $user->setBirthdate($d);
        }

        return $user;
    }

    public function enable(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $this->getLogger()->debug('data info', array(
            'data' => $data,
            'request' => $request->request->all()
        ));

        $user = $this->getEm()->getRepository('AppBundle:User')->findOneByConfirmationToken($data['token']);
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

            $this->getEm()->persist($address);
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

            $this->getEm()->persist($phone);
        }
        $user->setConfirmationToken(null);

        $this->getFosUserManager()->updateUser($user);

        return true;
    }

    public function manageRegister(Request $request)
    {
        $user = $this->getFosUserManager()->createUser();
        $form = $this->createRegisterForm($user);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $register = $form->getData();
                    $this->getLogger()->info(
                        'email',
                        array(
                            'register_class' => get_class($register),
                            'form_email' => $register->getEmail(),
                            'form_type' => $form->getExtraData(),
                            'register_username' => $register->getUsername(),
                            'request_all' => $request->request->all(),
                            'form_name' => $form->getName()
                        )
                    );

                    if ($searchUser = $this->getFosUserManager()->findUserByEmail($register->getEmail())) {
                        throw new \Exception("flash.message.user.already.exists", 400);
                    }
                    $extraData = $form->getExtraData();
                    $user = $this->addExtraData($extraData, $user);

                    if (isset($extraData['type']) and $extraData['type'] == 'advert') {
                        $user->addRole('ROLE_MONO');
                        $this->getLogger()->info('User ROLE MONO added');
                    } else {
                        $user->addRole('ROLE_PART');
                    }

                    $user->setNewsletter(true);

                    $token = substr($this->getTokenGenerator()->generateToken(), 0, 12);
                    $user->setConfirmationToken($token);
                    $encoder = $this->getEncoderFactory()->getEncoder($user);
                    $user->setPassword($encoder->encodePassword($register->getPassword(), $user->getSalt()));
                    $user->setEmail($register->getEmail());
                    $user->setUsername($register->getEmail());
                    $this->getFosUserManager()->updateUser($user);

                    $this->getLogger()->info('user should be verified', array('email' => $user->getEmail(), 'token' => $user->getConfirmationToken()));
                } catch (\Exception $e) {
                    $this->getLogger()->error(
                        'User creation fail',
                        array(
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'l.' => $e->getLine()
                        )
                    );
                    $form->addError(
                        new FormError($e->getMessage())
                    );
                }
            }
        }

        return array(
            array(
                'email' => $user->getEmail(),
                'confirmationToken' => $user->getConfirmationToken(),
                'username' => $user->getUsername(),
                'createdAt' => $user->getCreatedAt(),
            ),
            $form
        );
    }

    public function getUsers()
    {
        $users = new \ArrayIterator($this->getConnection()->fetchAll('SELECT * FROM user'));
        $count = 0;
        $total = $users->count();
        $this->logger->info(
            'import.table.user.array.users',
            array(
                'total' => $total
            )
        );
        return $users;
    }

    public function registerUser($userV1)
    {
        $this->getEm()->getConnection()->beginTransaction();

        try {
            $user = $this->fosUserManager->createUser();

            $user->setEmail($userV1['email']);
            $user->setNewsletter($userV1['newsletter']);
            $user->addRole("ROLE_USER");
            if ($userV1['pro'] == 2) {
                $user->addRole("ROLE_MONO");
            } elseif ($userV1['pro'] == 1) {
                $user->addRole("ROLE_MONO");
            } else {
                $user->addRole("ROLE_PART");
            }
            if ($userV1['email'] == 'romain.marecat@gmail.com') {
                $user->addRole("ROLE_ADMIN");
            }
            $newPwd = 'toto';
            $user->setPassword($newPwd);
            $user->setEnabled($userV1['validate']);
            $user->setFacebookId($userV1['facebook_id']);
            $user->setMangopayId($userV1['mangoId']);
            $user->setLastname($userV1['first_name']);
            $user->setFirstname($userV1['last_name']);
            $user->setAdminComment($userV1['admin_comment']);
            $user->setCreatedAt(new \DateTime($userV1['created_at']));
            $user->setUpdatedAt(new \DateTime($userV1['updated_at']));
            if (!is_null($userV1['birthdate'])) {
                $user->setBirthdate(new \DateTime($userV1['birthdate']));
            }

            $this->fosUserManager->updateUser($user);
            // $this->getEm()->persist($user);
            // $this->getEm()->flush();

            $this->getEm()->getConnection()->commit();
            $newId = $user->getId();

            return $newId;
        } catch (\Exception $e) {
            if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
            }
            $this->logger->error(
                'import.table.user.insert.query.error',
                array(
                    'user' => $userV1,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                )
            );
            throw $e;
            // interface with user to manage entity
        }
    }

    public function createAddress($email, $street, $postal_code, $city, $country)
    {

        $user = $this->getEm()->getRepository('AppBundle:User')->findOneByEmail($email);

        $address = new address;
        $address->setUser($user);
        $user->setAddress($address);
        $address->setStreet($street);
        $address->setPostalCode($postal_code);
        $address->setCity($city);
        $address->setCountry($country);

        $this->getEm()->persist($address);
        $this->getEm()->flush();
    }

    public function createPhone($email, $number, $countryCode, $countryNumber, $checked)
    {

        $user = $this->getEm()->getRepository('AppBundle:User')->findOneByEmail($email);

        $phone = new phone;
        $phone->setUser($user);
        $user->setPhone($phone);
        $phone->setNumber($number);
        $phone->setCountryCode($countryCode);
        $phone->setCountryNumber($countryNumber);

        if ($checked) {
            $phone->setChecked(true);
        } else {
            $phone->setChecked(false);
        }

        $this->getEm()->persist($phone);
        $this->getEm()->flush();
    }


    /**
     * Gets the value of em.
     *
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * Sets the value of em.
     *
     * @param mixed $em the em
     *
     * @return self
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * Gets the value of connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the value of connection.
     *
     * @param mixed $connection the connection
     *
     * @return self
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Gets the value of logger.
     *
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the value of logger.
     *
     * @param mixed $logger the logger
     *
     * @return self
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Gets the value of fosUserManager.
     *
     * @return mixed
     */
    public function getFosUserManager()
    {
        return $this->fosUserManager;
    }

    /**
     * Sets the value of fosUserManager.
     *
     * @param mixed $fosUserManager the fos user manager
     *
     * @return self
     */
    public function setFosUserManager(FOSUserManager $fosUserManager)
    {
        $this->fosUserManager = $fosUserManager;

        return $this;
    }

    /**
     * Gets the value of translator.
     *
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Sets the value of translator.
     *
     * @param mixed $translator the translator
     *
     * @return self
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Gets the value of formFactory.
     *
     * @return mixed
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Sets the value of formFactory.
     *
     * @param mixed $formFactory the form factory
     *
     * @return self
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;

        return $this;
    }

    /**
     * Gets the value of tokenGenerator.
     *
     * @return mixed
     */
    public function getTokenGenerator()
    {
        return $this->tokenGenerator;
    }

    /**
     * Sets the value of tokenGenerator.
     *
     * @param mixed $tokenGenerator the token generator
     *
     * @return self
     */
    public function setTokenGenerator(TokenGenerator $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;

        return $this;
    }

    /**
     * Gets the value of encoderFactory.
     *
     * @return mixed
     */
    public function getEncoderFactory()
    {
        return $this->encoderFactory;
    }

    /**
     * Sets the value of encoderFactory.
     *
     * @param mixed $encoderFactory the encoder factory
     *
     * @return self
     */
    public function setEncoderFactory(EncoderFactory $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;

        return $this;
    }

    /**
     * Gets the value of formParser.
     *
     * @return mixed
     */
    public function getFormParser()
    {
        return $this->formParser;
    }

    /**
     * Sets the value of formParser.
     *
     * @param mixed $formParser the form parser
     *
     * @return self
     */
    public function setFormParser($formParser)
    {
        $this->formParser = $formParser;

        return $this;
    }
}
