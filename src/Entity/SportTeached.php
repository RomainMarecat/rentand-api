<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * SportTeached
 *
 * @ORM\Table(name="sport_teached")
 * @ORM\Entity(repositoryClass="App\Repository\SportTeachedRepository")
 */
class SportTeached
{
    /**
     * @var string
     *
     * @ORM\Column(name="sport_teached_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"hidden", "getUser", "getMyUsers", "getSportTeachedById", "postEmailReminder"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="orderNumber", type="integer")
     * @JMS\Groups({"getUser"})
     */
    private $orderNumber;

    /**
     * @var array
     *
     * @ORM\Column(name="levels", type="array", nullable=true)
     * @JMS\Groups({"getUser"})
     */
    private $levels;

    /**
     * @var array
     *
     * @ORM\Column(name="ages", type="array", nullable=true)
     * @JMS\Groups({"getUser"})
     */
    private $ages;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sportsTeached", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE"))
     * @JMS\Groups({})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="sportsTeached", fetch="LAZY")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id", onDelete="CASCADE"))
     * @JMS\Groups({"getUser"})
     */
    private $sport;

    /**
     * @ORM\ManyToMany(targetEntity="Sport", inversedBy="sportTeachedSpecialites", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="specialities_sports",
     *      joinColumns={
     *          @ORM\JoinColumn(name="sport_teached_id", referencedColumnName="sport_teached_id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     *      }
     * )
     * @JMS\MaxDepth(1)
     * @JMS\Type("ArrayCollection<App\Entity\Sport>")
     * @JMS\Groups({"getUser"})
     */
    private $specialities;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="sportTeached", cascade={"persist","remove"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"getUser"})
     *
     */
    private $pictures;

    /**
     * @ORM\Column(name="translations", type="json")
     */
    private $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->specialities = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

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
     * Set orderNumber
     *
     * @param integer $orderNumber
     *
     * @return SportTeached
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return integer
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set levels
     *
     * @param array $levels
     *
     * @return SportTeached
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Get levels
     *
     * @return array
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return SportTeached
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sport
     *
     * @param Sport $sport
     *
     * @return SportTeached
     */
    public function setSport(Sport $sport = null)
    {
        $this->sport = $sport;

        return $this;
    }

    /**
     * Get sport
     *
     * @return \Entity\Sport
     */
    public function getSport()
    {
        return $this->sport;
    }


    /**
     * Add speciality
     *
     * @param Sport $speciality
     *
     * @return SportTeached
     */
    public function addSpeciality(Sport $speciality)
    {
        $this->specialities[] = $speciality;

        return $this;
    }

    /**
     * Remove speciality
     *
     * @param Sport $speciality
     */
    public function removeSpeciality(Sport $speciality)
    {
        $this->specialities->removeElement($speciality);
    }

    /**
     * Get specialities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpecialities()
    {
        return $this->specialities;
    }

    /**
     * Add picture
     *
     * @param Media $picture
     *
     * @return SportTeached
     */
    public function addPicture(Media $picture)
    {
        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove picture
     *
     * @param Media $picture
     */
    public function removePicture(Media $picture)
    {
        $this->pictures->removeElement($picture);
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Get translations
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("translations")
     * @JMS\Groups({"Default", "getUsers", "getUser", "getSportsTeachedByUser"})
     *
     */
    public function getTranslationsObject()
    {
        return $this->getTranslations();
    }

    /**
     * Get translations
     *
     */
    public function getTranslations()
    {
        return json_decode($this->translations, true);
    }

    /**
     * Set ages
     *
     * @param array $ages
     *
     * @return SportTeached
     */
    public function setAges($ages)
    {
        $this->ages = $ages;

        return $this;
    }

    /**
     * Get ages
     *
     * @return array
     */
    public function getAges()
    {
        return $this->ages;
    }

    public function setTranslations($translations): self
    {
        $this->translations = $translations;

        return $this;
    }
}
