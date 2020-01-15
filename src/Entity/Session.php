<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session extends Event
{
    /**
     * @ORM\Column(type="float")
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sport")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $sport;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sport")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $speciality;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="city_id")
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MeetingPoint")
     * @ORM\JoinColumn(name="meeting_id", referencedColumnName="meeting_id")
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $meetingPoint;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $nbPersons;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $duration;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="sessions")
     * @ORM\JoinTable(name="sessions_customers",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="event_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")})
     * @JMS\Groups({"getSessionsByUser"})
     */
    private $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(User $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setSession($this);
        }

        return $this;
    }

    public function removeCustomer(User $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            // set the owning side to null (unless already changed)
            if ($customer->getSession() === $this) {
                $customer->setSession(null);
            }
        }

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(?Sport $sport): self
    {
        $this->sport = $sport;

        return $this;
    }

    public function getSpeciality(): ?Sport
    {
        return $this->speciality;
    }

    public function setSpeciality(?Sport $speciality): self
    {
        $this->speciality = $speciality;

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

    public function getMeetingPoint(): ?MeetingPoint
    {
        return $this->meetingPoint;
    }

    public function setMeetingPoint(?MeetingPoint $meetingPoint): self
    {
        $this->meetingPoint = $meetingPoint;

        return $this;
    }

    public function getNbPersons(): ?int
    {
        return $this->nbPersons;
    }

    public function setNbPersons(?int $nbPersons): self
    {
        $this->nbPersons = $nbPersons;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
