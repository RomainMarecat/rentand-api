<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionTypeRepository")
 */
class SessionType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="session_type_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $name;

    /**
     * @ORM\Column(name="max_persons", type="integer", nullable=true)
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $maxPersons;

    /**
     * @ORM\Column(name="booking_delay", type="integer", nullable=true)
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $bookingDelay;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pause;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\OnlineSession", inversedBy="sessionType", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="online_session_id", referencedColumnName="online_session_id", nullable=false)
     */
    private $onlineSession;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMaxPersons(): ?int
    {
        return $this->maxPersons;
    }

    public function setMaxPersons(?int $maxPersons): self
    {
        $this->maxPersons = $maxPersons;

        return $this;
    }

    public function getBookingDelay(): ?int
    {
        return $this->bookingDelay;
    }

    public function setBookingDelay(?int $bookingDelay): self
    {
        $this->bookingDelay = $bookingDelay;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPause(): ?int
    {
        return $this->pause;
    }

    public function setPause(?int $pause): self
    {
        $this->pause = $pause;

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
}
