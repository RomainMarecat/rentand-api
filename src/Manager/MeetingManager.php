<?php

namespace App\Manager;

/**
 * Class MeetingManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class MeetingManager
{
    protected $em;

    protected $logger;

    public function get($meeting)
    {
        return $this->getEm()->getRepository('App:MeetingPoint')->findOneById($meeting);
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
