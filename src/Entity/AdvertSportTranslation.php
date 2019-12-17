<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AdvertSportTranslation
 *
 * @ORM\Table(name="advert_sport_translation")
 * @ORM\Entity
 */
class AdvertSportTranslation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="advert_sport_translation_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 2,
     *      exactMessage = "This value should have exactly {{ limit }} characters",
     * )
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="handisport", type="string", length=255, nullable=true)
     * @JMS\Accessor(getter="getHandisport",setter="setHandisport")
     */
    protected $handisport;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Accessor(getter="getDescription",setter="setDescription")
     */
    protected $description;


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
     * @ORM\ManyToOne(targetEntity="AdvertSport", inversedBy="translations", cascade={"persist"})
     * @ORM\JoinColumn(name="advert_sport_id", referencedColumnName="advert_sport_id", onDelete="CASCADE"))
     * @Assert\NotBlank()
     * @JMS\Groups({"hidden"})
     */
    protected $advertSport;

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
     * Set locale
     *
     * @param string $locale
     *
     * @return AdvertSportTranslation
     */
    public function setLocale($locale)
    {
        $this->locale = strtolower($locale);

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set handisport
     *
     * @param string $handisport
     *
     * @return AdvertSportTranslation
     */
    public function setHandisport($handisport)
    {
        $this->handisport = ucfirst($handisport);

        return $this;
    }

    /**
     * Get handisport
     *
     * @return string
     */
    public function getHandisport()
    {
        return $this->handisport;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return AdvertSportTranslation
     */
    public function setDescription($description)
    {
        $this->description = ucfirst($description);

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AdvertSportTranslation
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
     * @return AdvertSportTranslation
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
     * Set advertSport
     *
     * @param AdvertSport $advertSport
     *
     * @return AdvertSportTranslation
     */
    public function setAdvertSport(AdvertSport $advertSport = null)
    {
        $this->advertSport = $advertSport;

        return $this;
    }

    /**
     * Get advertSport
     *
     * @return \Entity\AdvertSport
     */
    public function getAdvertSport()
    {
        return $this->advertSport;
    }
}
