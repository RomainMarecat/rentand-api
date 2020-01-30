<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * MeetingPoint
 *
 * @ORM\Table(name="meeting_point")
 * @ORM\Entity(repositoryClass="App\Repository\MeetingRepository")
 */
class MeetingPoint
{
    /**
     * @var int
     *
     * @ORM\Column(name="meeting_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getCity", "getUser", "addSession"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @JMS\Groups({"getCity", "getUser", "addSession"})
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="lng", type="float")
     * @JMS\Groups({"getCity", "getUser", "addSession"})
     */
    private $lng;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float")
     * @JMS\Groups({"getCity", "getUser", "addSession"})
     */
    private $lat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="meetings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE"))
     * @JMS\Groups({})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="meetingPoints")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="city_id", onDelete="CASCADE"))
     * @JMS\Groups({"getUser", "getCity"})
     */
    private $city;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
