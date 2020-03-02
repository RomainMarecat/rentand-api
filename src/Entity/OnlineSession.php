<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

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
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="SportTeached", cascade={"persist"}, inversedBy="onlineSessions")
     * @ORM\JoinColumn(name="sport_teached_id", referencedColumnName="sport_teached_id")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $sportTeached;

    /**
     * @ORM\ManyToOne(targetEntity="CityTeached", cascade={"persist"})
     * @ORM\JoinColumn(name="city_teached_id", referencedColumnName="city_teached_id")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $cityTeached;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $price;

    /**
     * @ORM\Column(name="start_date", type="date")
     * @JMS\Groups({"getOnlineSessions"})
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $startDate;

    /**
     * @ORM\Column(name="end_date", type="date")
     * @JMS\Groups({"getOnlineSessions"})
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $endDate;

    /**
     * @ORM\Column(name="start_time", type="time")
     * @JMS\Groups({"getOnlineSessions"})
     * @JMS\Type("DateTime<'H:i'>")
     */
    private $startTime;

    /**
     * @ORM\Column(name="end_time", type="time")
     * @JMS\Groups({"getOnlineSessions"})
     * @JMS\Type("DateTime<'H:i'>")
     */
    private $endTime;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="onlineSession")
     */
    private $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }


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

    public function getSportTeached(): ?SportTeached
    {
        return $this->sportTeached;
    }

    public function setSportTeached(?SportTeached $sportTeached): self
    {
        $this->sportTeached = $sportTeached;

        return $this;
    }

    public function getCityTeached(): ?CityTeached
    {
        return $this->cityTeached;
    }

    public function setCityTeached(?CityTeached $cityTeached): self
    {
        $this->cityTeached = $cityTeached;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setOnlineSession($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getOnlineSession() === $this) {
                $session->setOnlineSession(null);
            }
        }

        return $this;
    }
}
