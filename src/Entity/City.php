<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * City
 *
 * @ORM\Table(name="city_city")
 * @ORM\Entity(repositoryClass="Repository\CityRepository")
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
     * @JMS\Groups({"getSimpleSearch", "getAdvancedSearch", "postSimpleSearch", "countPostSimpleSearch"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="googleId", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $googleId;

    /**
     * @var float
     *
     * @ORM\Column(name="lng", type="float")
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $lng;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float")
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="north", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $north;

    /**
     * @var float
     *
     * @ORM\Column(name="south", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $south;

    /**
     * @var float
     *
     * @ORM\Column(name="east", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $east;

    /**
     * @var float
     *
     * @ORM\Column(name="west", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $west;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="Advert", mappedBy="cities", fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden"})
     */
    private $adverts;

    /**
     * @ORM\OneToMany(targetEntity="Meeting", mappedBy="city", cascade={"remove"}, fetch="EXTRA_LAZY")
     * @JMS\Type("ArrayCollection<Entity\Meeting>")
     * @JMS\Groups({"hidden"})
     */
    protected $meetings;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return City
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     *
     * @return City
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return City
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return City
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set north
     *
     * @param float $north
     *
     * @return City
     */
    public function setNorth($north)
    {
        $this->north = $north;

        return $this;
    }

    /**
     * Get north
     *
     * @return float
     */
    public function getNorth()
    {
        return $this->north;
    }

    /**
     * Set south
     *
     * @param float $south
     *
     * @return City
     */
    public function setSouth($south)
    {
        $this->south = $south;

        return $this;
    }

    /**
     * Get south
     *
     * @return float
     */
    public function getSouth()
    {
        return $this->south;
    }

    /**
     * Set east
     *
     * @param float $east
     *
     * @return City
     */
    public function setEast($east)
    {
        $this->east = $east;

        return $this;
    }

    /**
     * Get east
     *
     * @return float
     */
    public function getEast()
    {
        return $this->east;
    }

    /**
     * Set west
     *
     * @param float $west
     *
     * @return City
     */
    public function setWest($west)
    {
        $this->west = $west;

        return $this;
    }

    /**
     * Get west
     *
     * @return float
     */
    public function getWest()
    {
        return $this->west;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return City
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return City
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->adverts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add advert
     *
     * @param Advert $advert
     *
     * @return City
     */
    public function addAdvert(Advert $advert)
    {
        $this->adverts[] = $advert;

        return $this;
    }

    /**
     * Remove advert
     *
     * @param Advert $advert
     */
    public function removeAdvert(Advert $advert)
    {
        $this->adverts->removeElement($advert);
    }

    /**
     * Get adverts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdverts()
    {
        return $this->adverts;
    }

    /**
     * Add meeting
     *
     * @param Meeting $meeting
     *
     * @return City
     */
    public function addMeeting(Meeting $meeting)
    {
        $this->meetings[] = $meeting;

        return $this;
    }

    /**
     * Remove meeting
     *
     * @param Meeting $meeting
     */
    public function removeMeeting(Meeting $meeting)
    {
        $this->meetings->removeElement($meeting);
    }

    /**
     * Get meetings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMeetings()
    {
        return $this->meetings;
    }
}
