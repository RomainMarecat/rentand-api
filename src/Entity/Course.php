<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Course
 *
 * @ORM\Table(name="course_course")
 * @ORM\Entity(repositoryClass="Repository\CourseRepository")
 */
class Course
{
    /**
     * @var string
     *
     * @ORM\Column(name="course_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mongo_id", type="string", length=80)
     */
    protected $mongoId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_person", type="integer")
     */
    private $nbPerson;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var float
     *
     * @ORM\Column(name="unit_price", type="float")
     */
    private $unitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="course_type", type="string", length=45)
     */
    private $courseType;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=80)
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="sport", type="string", length=80)
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    private $sport;

    /**
     * @var string
     *
     * @ORM\Column(name="speciality", type="string", length=80, nullable=true)
     */
    private $speciality;

    /**
     * @var string
     *
     * @ORM\Column(name="meeting", type="string", length=80)
     */
    private $meeting;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=80)
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=80)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=55)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=55)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=65)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     */
    private $phone;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Booking", inversedBy="courses")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="booking_id", onDelete="SET NULL")
     * @JMS\Groups({"hidden"})
     */
    protected $booking;

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
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Course
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Course
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set booking
     *
     * @param string $booking
     *
     * @return Course
     */
    public function setBooking($booking)
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Get booking
     *
     * @return string
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Set nbPerson
     *
     * @param integer $nbPerson
     *
     * @return Course
     */
    public function setNbPerson($nbPerson)
    {
        $this->nbPerson = $nbPerson;

        return $this;
    }

    /**
     * Get nbPerson
     *
     * @return int
     */
    public function getNbPerson()
    {
        return $this->nbPerson;
    }

    /**
     * Set unitPrice
     *
     * @param float $unitPrice
     *
     * @return Course
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set courseType
     *
     * @param string $courseType
     *
     * @return Course
     */
    public function setCourseType($courseType)
    {
        $this->courseType = $courseType;

        return $this;
    }

    /**
     * Get courseType
     *
     * @return string
     */
    public function getCourseType()
    {
        return $this->courseType;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Course
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Course
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Course
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Course
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Course
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
     * @param string $updatedAt
     *
     * @return Course
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Course
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Course
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set sport
     *
     * @param string $sport
     *
     * @return Course
     */
    public function setSport($sport)
    {
        $this->sport = $sport;

        return $this;
    }

    /**
     * Get sport
     *
     * @return string
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * Set speciality
     *
     * @param string $speciality
     *
     * @return Course
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;

        return $this;
    }

    /**
     * Get speciality
     *
     * @return string
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * Set meeting
     *
     * @param string $meeting
     *
     * @return Course
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;

        return $this;
    }

    /**
     * Get meeting
     *
     * @return string
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * Set level
     *
     * @param string $level
     *
     * @return Course
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Course
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set mongoId
     *
     * @param string $mongoId
     *
     * @return Course
     */
    public function setMongoId($mongoId)
    {
        $this->mongoId = $mongoId;

        return $this;
    }

    /**
     * Get mongoId
     *
     * @return string
     */
    public function getMongoId()
    {
        return $this->mongoId;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return Course
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer
     */
    public function getAge()
    {
        return $this->age;
    }
}
