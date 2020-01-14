<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionPriceRepository")
 */
class SessionPrice
{
    /**
     * @ORM\Column(name="session_price_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OnlineSession", inversedBy="sessionPrices")
     * @ORM\JoinColumn(name="online_session_id", referencedColumnName="online_session_id", nullable=false)
     */
    private $onlineSession;

    /**
     * @ORM\Column(name="start_date", type="date")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $startDate;

    /**
     * @ORM\Column(name="end_date", type="date")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $endDate;

    /**
     * @ORM\Column(name="start_time", type="time")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $startTime;

    /**
     * @ORM\Column(name="end_time", type="time")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $endTime;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOnlineSession(): ?OnlineSession
    {
        return $this->onlineSession;
    }

    public function setOnlineSession(?OnlineSession $onlineSession): self
    {
        $this->onlineSession = $onlineSession;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }
}
