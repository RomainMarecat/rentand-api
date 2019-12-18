<?php

namespace App\Manager\Admin;

use Doctrine\ORM\EntityManager;
use Helper\RegexHelper;
use Monolog\Logger;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

class UserManager
{
    protected $logger;

    protected $em;

    protected $formFactory;

    protected $regexHelper;

    protected $kernelRootDir;

    protected $mangoPayService;

    protected $fosUserManager;

    public function adminCget()
    {
        return $this->getEm()->getRepository('App:User')->adminCget();
    }

    public function adminCgetErrors()
    {
        return $this->getEm()->getRepository('App:User')->adminCgetErrors();
    }

    public function getUsersByMangopayId($mangopayId)
    {
        $users = $this->getEm()->getRepository('App:User')->findUsersIdByMangopayId($mangopayId);

        foreach ($users as $key => $user) {
            if (isset($user['address']['country'])) {
                $users[$key]['country'] = $user['address']['country'];
            }
        }
        return $users;
    }

    public function postUsersByMangopayId(Request $request)
    {
        if ($request->isMethod('POST')) {
            $users = $request->request->get('users');
        }

        $usersIds = array_unique(
            array_filter(
                array_map(
                    function ($user) {
                        return isset($user['id']) ? $user['id'] : null;
                    },
                    $users
                )
            )
        );
        $this->getLogger()->debug('users info', array('users' => $users));

        $dataUsers = $this->getEm()->getRepository('App:User')->findUserIn($usersIds);
        $this->getLogger()->debug('users info', array('users' => $dataUsers));

        $this->getEm()->getConnection()->beginTransaction();
        foreach ($dataUsers as $dataUser) {
            foreach ($users as $k => $u) {
                if (isset($u['id']) and $u['id'] == $dataUser->getId() and isset($u['mangopayId'])) {
                    $this->getLogger()->debug('users info', array('u_mangopayId' => isset($u['mangopayId']) ? $u['mangopayId'] : null, 'u_id' => isset($u['id']) ? $u['id'] : null, 'id' => $dataUser->getId()));
                    try {
                        $dataUser->setMangopayId($u['mangopayId']);
                        $this->getFosUserManager()->updateUser($dataUser);
                    } catch (\Exception $e) {
                        $this->getLogger()->error(
                            'createUserNatural and merge fail',
                            array(
                                'message' => $e->getMessage(),
                                'l.' => $e->getLine(),
                                'trace' => $e->getTraceAsString(),
                            )
                        );
                    }
                }
            }
        }
        $this->getEm()->getConnection()->commit();

        $this->getLogger()->debug('users processed', array('users' => $users));

        return $users;
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
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
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
     * Gets the value of regexHelper.
     *
     * @return mixed
     */
    public function getRegexHelper()
    {
        return $this->regexHelper;
    }

    /**
     * Sets the value of regexHelper.
     *
     * @param mixed $regexHelper the regex helper
     *
     * @return self
     */
    public function setRegexHelper(RegexHelper $regexHelper)
    {
        $this->regexHelper = $regexHelper;

        return $this;
    }

    /**
     * Gets the value of kernelRootDir.
     *
     * @return mixed
     */
    public function getKernelRootDir()
    {
        return $this->kernelRootDir;
    }

    /**
     * Sets the value of kernelRootDir.
     *
     * @param mixed $kernelRootDir the kernel root dir
     *
     * @return self
     */
    public function setKernelRootDir($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;

        return $this;
    }

    /**
     * Gets the value of mangoPayService.
     *
     * @return mixed
     */
    public function getMangoPayService()
    {
        return $this->mangoPayService;
    }

    /**
     * Sets the value of mangoPayService.
     *
     * @param mixed $mangoPayService the mango pay services
     *
     * @return self
     */
    public function setMangoPayService($mangoPayService)
    {
        $this->mangoPayService = $mangoPayService;

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
    public function setFosUserManager($fosUserManager)
    {
        $this->fosUserManager = $fosUserManager;

        return $this;
    }
}
