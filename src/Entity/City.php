<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @JMS\ExclusionPolicy("none")
 */
class City
{
    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getCity"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @JMS\Groups({"getCity"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="googleId", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $googleId;

    /**
     * @var float
     *
     * @ORM\Column(name="lng", type="float")
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @JMS\Groups({"getCity"})
     */
    private $lng;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float")
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @JMS\Groups({"getCity"})
     */
    private $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="north", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $north;

    /**
     * @var float
     *
     * @ORM\Column(name="south", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $south;

    /**
     * @var float
     *
     * @ORM\Column(name="east", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $east;

    /**
     * @var float
     *
     * @ORM\Column(name="west", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $west;

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
     * @ORM\OneToMany(targetEntity="MeetingPoint", mappedBy="city")
     */
    private $meetingPoints;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meetingPoints = new ArrayCollection();
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("meeting_points")
     * @JMS\Groups({"getCity"})
     * @return array
     */
    public function getMeetingPointsData()
    {
        return $this->meetingPoints->toArray();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(string $googleId): self
    {
        $this->googleId = $googleId;

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

    public function getNorth(): ?float
    {
        return $this->north;
    }

    public function setNorth(?float $north): self
    {
        $this->north = $north;

        return $this;
    }

    public function getSouth(): ?float
    {
        return $this->south;
    }

    public function setSouth(?float $south): self
    {
        $this->south = $south;

        return $this;
    }

    public function getEast(): ?float
    {
        return $this->east;
    }

    public function setEast(?float $east): self
    {
        $this->east = $east;

        return $this;
    }

    public function getWest(): ?float
    {
        return $this->west;
    }

    public function setWest(?float $west): self
    {
        $this->west = $west;

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

    /**
     * @return Collection|MeetingPoint[]
     */
    public function getMeetingPoints(): Collection
    {
        return $this->meetingPoints;
    }

    public function addMeetingPoint(MeetingPoint $meetingPoint): self
    {
        if (!$this->meetingPoints->contains($meetingPoint)) {
            $this->meetingPoints[] = $meetingPoint;
            $meetingPoint->setCity($this);
        }

        return $this;
    }

    public function removeMeetingPoint(MeetingPoint $meetingPoint): self
    {
        if ($this->meetingPoints->contains($meetingPoint)) {
            $this->meetingPoints->removeElement($meetingPoint);
            // set the owning side to null (unless already changed)
            if ($meetingPoint->getCity() === $this) {
                $meetingPoint->setCity(null);
            }
        }

        return $this;
    }
}
