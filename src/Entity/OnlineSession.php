<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OnlineSessionRepository")
 */
class OnlineSession
{
    /**
     * @var string
     *
     * @ORM\Column(name="online_session_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(name="session_type", type="object")
     */
    private $sessionType;

    /**
     * @ORM\ManyToOne(targetEntity="Sport")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     */
    private $sportTeached;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="city_id")
     */
    private $cityTeached;

    /**
     * @ORM\Column(name="prices", type="array")
     */
    private $prices = [];

    /**
     * @ORM\Column(name="date_range", type="object")
     */
    private $dateRange;

    /**
     * @ORM\Column(name="time_range", type="object")
     */
    private $timeRange;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return OnlineSession
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * @param mixed $sessionType
     *
     * @return OnlineSession
     */
    public function setSessionType($sessionType)
    {
        $this->sessionType = $sessionType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSportTeached()
    {
        return $this->sportTeached;
    }

    /**
     * @param mixed $sportTeached
     *
     * @return OnlineSession
     */
    public function setSportTeached($sportTeached)
    {
        $this->sportTeached = $sportTeached;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCityTeached()
    {
        return $this->cityTeached;
    }

    /**
     * @param mixed $cityTeached
     *
     * @return OnlineSession
     */
    public function setCityTeached($cityTeached)
    {
        $this->cityTeached = $cityTeached;
        return $this;
    }

    /**
     * @return array
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @param array $prices
     *
     * @return OnlineSession
     */
    public function setPrices(array $prices): OnlineSession
    {
        $this->prices = $prices;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateRange()
    {
        return $this->dateRange;
    }

    /**
     * @param mixed $dateRange
     *
     * @return OnlineSession
     */
    public function setDateRange($dateRange)
    {
        $this->dateRange = $dateRange;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeRange()
    {
        return $this->timeRange;
    }

    /**
     * @param mixed $timeRange
     *
     * @return OnlineSession
     */
    public function setTimeRange($timeRange)
    {
        $this->timeRange = $timeRange;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return OnlineSession
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
