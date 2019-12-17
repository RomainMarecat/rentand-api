<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SportTranslation
 *
 * @ORM\Table(name="sport_translation")
 * @ORM\Entity(repositoryClass="Repository\SportTranslationRepository")
 * @JMS\ExclusionPolicy("none")
 */
class SportTranslation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="sport_translation_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2)
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @JMS\Groups({"Default", "getSimpleSearch", "getAdvancedSearch"})
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="search", type="string", length=255, nullable=true)
     */
    protected $search;

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
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="translations")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id", onDelete="CASCADE"))
     * @Assert\NotBlank()
     * @JMS\Groups({"hidden", "getFormAdvert"})
     */
    protected $sport;


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
     * @return SportTranslation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

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
     * Set title
     *
     * @param string $title
     *
     * @return SportTranslation
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
     * Set search
     *
     * @param string $search
     *
     * @return SportTranslation
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SportTranslation
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
     * @return SportTranslation
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
     * Set sport
     *
     * @param Sport $sport
     *
     * @return SportTranslation
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
}
