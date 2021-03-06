<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityTeachedRepository")
 */
class CityTeached
{
    /**
     * @var string
     *
     * @ORM\Column(name="city_teached_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getCity", "getOnlineSessions", "getUser"})
     */
    private $id;

    /**
     * @ORM\Column(name="personal_meeting_point_accepted", type="boolean")
     * @JMS\Groups({"getCity", "getOnlineSessions", "getUser"})
     */
    private $personalMeetingPointAccepted;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="City", cascade={"persist"})
     * @ORM\JoinColumn(name="city_id", referencedColumnName="city_id")
     * @JMS\Groups({"getCity", "getOnlineSessions", "getUser"})
     */
    private $city;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="citiesTeached", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @JMS\Groups({"getCity"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonalMeetingPointAccepted(): ?bool
    {
        return $this->personalMeetingPointAccepted;
    }

    public function setPersonalMeetingPointAccepted(bool $personalMeetingPointAccepted): self
    {
        $this->personalMeetingPointAccepted = $personalMeetingPointAccepted;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

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
}
