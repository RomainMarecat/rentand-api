<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Sport
 *
 * @ORM\Table(name="sport_sport")
 * @ORM\Entity(repositoryClass="Repository\SportRepository")
 * @JMS\ExclusionPolicy("none")
 */
class Sport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="sport_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"Default", "getSimpleSearch", "getAdvancedSearch", "postSimpleSearch", "countPostSimpleSearch", "Default"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, separator="-", updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    protected $level;

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
     * @ORM\OneToMany(targetEntity="SportTranslation", mappedBy="sport", indexBy="locale", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Type("ArrayCollection<Entity\SportTranslation>")
     * @JMS\Groups({"hidden", "getSports", "getSport", "getFormAdvert", "getFormSearch", "getFormSport", "getFormFamily", "getAdvert", "getFormAdvert", "getFamiliesByParent", "getSimpleSearch", "postSimpleSearch", "postAdvancedSearch", "getAdvancedSearch", "newPreBookings", "getSport", "getAdvertSportById"})
     */
    protected $translations;

    /**
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="sport_id", onDelete="CASCADE")
     * @JMS\Groups({"hidden"})
     * @JMS\MaxDepth(2)
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="AdvertSport", mappedBy="sport", fetch="EXTRA_LAZY")
     * @JMS\Exclude
     * @JMS\Groups({"hidden"})
     */
    protected $advert;

    /**
     * @ORM\ManyToMany(targetEntity="AdvertSport", mappedBy="specialities")
     * @JMS\Exclude
     * @JMS\Groups({"hidden", "getFamiliesByParent"})
     * @JMS\MaxDepth(1)
     */
    protected $advertSports;

    /**
     * @ORM\OneToMany(targetEntity="Sport", mappedBy="parent", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden", "getFormAdvert", "getSport"})
     */
    protected $children;

    /**
     * @ORM\ManyToMany(targetEntity="Family", mappedBy="sports", fetch="EXTRA_LAZY")
     * @JMS\Exclude
     * @JMS\Groups({"hidden"})
     */
    protected $families;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->advert = new ArrayCollection();
        $this->advertSports = new ArrayCollection();
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
     * Set level
     *
     * @param integer $level
     *
     * @return Sport
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Sport
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
     * @return Sport
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
     * Set parent
     *
     * @param Sport $parent
     *
     * @return Sport
     */
    public function setParent(Sport $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Entity\Sport
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add translation
     *
     * @param SportTranslation $translation
     *
     * @return Sport
     */
    public function addTranslation(SportTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param SportTranslation $translation
     */
    public function removeTranslation(SportTranslation $translation)
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
     * Add child
     *
     * @param Sport $child
     *
     * @return Sport
     */
    public function addChild(Sport $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param Sport $child
     */
    public function removeChild(Sport $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add advert
     *
     * @param AdvertSport $advert
     *
     * @return Sport
     */
    public function addAdvert(AdvertSport $advert)
    {
        $this->advert[] = $advert;

        return $this;
    }

    /**
     * Remove advert
     *
     * @param AdvertSport $advert
     */
    public function removeAdvert(AdvertSport $advert)
    {
        $this->advert->removeElement($advert);
    }

    /**
     * Get advert
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Add advertSport
     *
     * @param AdvertSport $advertSport
     *
     * @return Sport
     */
    public function addAdvertSport(AdvertSport $advertSport)
    {
        $this->advertSports[] = $advertSport;

        return $this;
    }

    /**
     * Remove advertSport
     *
     * @param AdvertSport $advertSport
     */
    public function removeAdvertSport(AdvertSport $advertSport)
    {
        $this->advertSports->removeElement($advertSport);
    }

    /**
     * Get advertSports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdvertSports()
    {
        return $this->advertSports;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Sport
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Sport
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add family
     *
     * @param Family $family
     *
     * @return Sport
     */
    public function addFamily(Family $family)
    {
        $this->families[] = $family;

        return $this;
    }

    /**
     * Remove family
     *
     * @param Family $family
     */
    public function removeFamily(Family $family)
    {
        $this->families->removeElement($family);
    }

    /**
     * Get families
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFamilies()
    {
        return $this->families;
    }
}
