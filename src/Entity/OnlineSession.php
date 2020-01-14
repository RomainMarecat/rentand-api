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
     * @ORM\ManyToOne(targetEntity="SportTeached", cascade={"persist"})
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
     * @ORM\OneToMany(targetEntity="App\Entity\SessionPrice", mappedBy="onlineSession", orphanRemoval=true)
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $sessionPrices;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SessionType", mappedBy="onlineSession", cascade={"persist", "remove"})
     * @JMS\Groups({"getOnlineSessions"})
     */
    private $sessionType;

    public function __construct()
    {
        $this->sessionPrices = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    /**
     * @return Collection|SessionPrice[]
     */
    public function getSessionPrices(): Collection
    {
        return $this->sessionPrices;
    }

    public function addSessionPrice(SessionPrice $sessionPrice): self
    {
        if (!$this->sessionPrices->contains($sessionPrice)) {
            $this->sessionPrices[] = $sessionPrice;
            $sessionPrice->setOnlineSession($this);
        }

        return $this;
    }

    public function removeSessionPrice(SessionPrice $sessionPrice): self
    {
        if ($this->sessionPrices->contains($sessionPrice)) {
            $this->sessionPrices->removeElement($sessionPrice);
            // set the owning side to null (unless already changed)
            if ($sessionPrice->getOnlineSession() === $this) {
                $sessionPrice->setOnlineSession(null);
            }
        }

        return $this;
    }

    public function getSessionType(): ?SessionType
    {
        return $this->sessionType;
    }

    public function setSessionType(?SessionType $sessionType): self
    {
        $this->sessionType = $sessionType;

        // set (or unset) the owning side of the relation if necessary
        $newOnlineSession = null === $sessionType ? null : $this;
        if ($sessionType->getOnlineSession() !== $newOnlineSession) {
            $sessionType->setOnlineSession($newOnlineSession);
        }

        return $this;
    }
}
