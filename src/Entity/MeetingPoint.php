<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * MeetingPoint
 *
 * @ORM\Table(name="meeting_meeting")
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
     * @JMS\Groups({"getCity"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @JMS\Groups({"getCity"})
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="lng", type="float")
     * @JMS\Groups({"getCity"})
     */
    private $lng;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float")
     * @JMS\Groups({"getCity"})
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

    /**
     * Get id
     *
     * @return int
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
     * @return MeetingPoint
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
     * Set lng
     *
     * @param float $lng
     *
     * @return MeetingPoint
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
     * @return MeetingPoint
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return MeetingPoint
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
     * @return MeetingPoint
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
     * Set city
     *
     * @param City $city
     *
     * @return MeetingPoint
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
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
     * @return MeetingPoint
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
