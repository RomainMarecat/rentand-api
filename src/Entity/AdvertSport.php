<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * AdvertSport
 *
 * @ORM\Table(name="advert_sport")
 * @ORM\Entity(repositoryClass="Repository\AdvertSportRepository")
 */
class AdvertSport
{
    /**
     * @var int
     *
     * @ORM\Column(name="advert_sport_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="orderNumber", type="integer")
     */
    protected $orderNumber;

    /**
     * @var array
     *
     * @ORM\Column(name="levels", type="array", nullable=true)
     */
    protected $levels;

    /**
     * @var array
     *
     * @ORM\Column(name="ages", type="array", nullable=true)
     */
    protected $ages;

    /**
     * @ORM\ManyToOne(targetEntity="Advert", inversedBy="sports", fetch="LAZY")
     * @ORM\JoinColumn(name="advert_id", referencedColumnName="advert_id", onDelete="CASCADE"))
     * @JMS\Groups({"hidden"})
     */
    protected $advert;

    /**
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="advert", fetch="LAZY")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id", onDelete="CASCADE"))
     * @JMS\Groups({"hidden", "getAdvert", "getMyAdverts", "getAdvertSportById", "postEmailReminder"})
     */
    protected $sport;

    /**
     * @ORM\ManyToMany(targetEntity="Sport", inversedBy="advertSports", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="specialities_sports",
     *      joinColumns={
     *          @ORM\JoinColumn(name="advert_sport_id", referencedColumnName="advert_sport_id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     *      }
     * )
     * @JMS\MaxDepth(1)
     * @JMS\Type("ArrayCollection<App\Entity\Sport>")
     * @JMS\Groups({"hidden", "postSimpleSearch", "countPostSimpleSearch", "getAdvert"})
     */
    protected $specialities;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="advertSport", cascade={"persist","remove"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden", "getAdvert", "getAdvertAdvertSports"})
     *
     */
    protected $pictures;

    /**
     * @ORM\OneToMany(
     *      targetEntity="AdvertSportTranslation",
     *      mappedBy="advertSport",
     *      indexBy="locale",
     *      cascade={"remove", "persist"},
     *      fetch="LAZY"
     * )
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden"})
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->specialities = new ArrayCollection();
        $this->translations = new ArrayCollection();
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
     * @return AdvertSport
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
     * @return AdvertSport
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
     * Set advert
     *
     * @param Advert $advert
     *
     * @return AdvertSport
     */
    public function setAdvert(Advert $advert = null)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Set sport
     *
     * @param Sport $sport
     *
     * @return AdvertSport
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
     * @return AdvertSport
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
     * @param Image $picture
     *
     * @return AdvertSport
     */
    public function addPicture(Image $picture)
    {
        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove picture
     *
     * @param Image $picture
     */
    public function removePicture(Image $picture)
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
     * Add translation
     *
     * @param AdvertSportTranslation $translation
     *
     * @return AdvertSport
     */
    public function addTranslation(AdvertSportTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param AdvertSportTranslation $translation
     */
    public function removeTranslation(AdvertSportTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set ages
     *
     * @param array $ages
     *
     * @return AdvertSport
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
}
