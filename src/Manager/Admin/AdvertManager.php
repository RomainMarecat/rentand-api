<?php

namespace App\Manager\Admin;

use Doctrine\ORM\EntityManager;
use Helper\RegexHelper;
use Monolog\Logger;
use Symfony\Component\Form\FormFactory;

class AdvertManager
{
    protected $logger;

    protected $em;

    protected $formFactory;

    protected $regexHelper;

    public function adminCget()
    {
        return $this->getEm()->getRepository('App:Advert')->adminCget();
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
}
