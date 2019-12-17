<?php

namespace App\Manager;

/**
 * Class PhoneManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class PhoneManager
{
    protected $em;

    protected $logger;

    public function get($phone)
    {
        return $this->getEm()->getRepository('AppBundle:Phone')->findOneById($phone);
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
    public function setEm($em)
    {
        $this->em = $em;

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
}
