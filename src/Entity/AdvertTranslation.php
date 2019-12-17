<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AdvertTranslation
 *
 * @ORM\Table(name="advert_translation")
 * @ORM\Entity
 */
class AdvertTranslation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="advert_translation_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2, nullable=false)
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, separator="-", updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description1", type="text", nullable=true)
     * @JMS\Accessor(getter="getDescription1",setter="setDescription1")
     */
    protected $description1;

    /**
     * @var string
     *
     * @ORM\Column(name="description2", type="text", nullable=true)
     * @JMS\Accessor(getter="getDescription2",setter="setDescription2")
     */
    protected $description2;

    /**
     * @var string
     *
     * @ORM\Column(name="description3", type="text", nullable=true)
     * @JMS\Accessor(getter="getDescription3",setter="setDescription3")
     */
    protected $description3;

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
     * @ORM\ManyToOne(targetEntity="Advert", inversedBy="translations")
     * @ORM\JoinColumn(name="advert_id", referencedColumnName="advert_id", onDelete="CASCADE"))
     * @Assert\NotBlank()
     * @JMS\Groups({"hidden"})
     */
    protected $advert;

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
     * @return AdvertTranslation
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
     * Set description1
     *
     * @param string $description1
     *
     * @return AdvertTranslation
     */
    public function setDescription1($description1)
    {
        $this->description1 = ucfirst($description1);

        return $this;
    }

    /**
     * Get description1
     *
     * @return string
     */
    public function getDescription1()
    {
        return $this->description1;
    }

    /**
     * Set description2
     *
     * @param string $description2
     *
     * @return AdvertTranslation
     */
    public function setDescription2($description2)
    {
        $this->description2 = ucfirst($description2);

        return $this;
    }

    /**
     * Get description2
     *
     * @return string
     */
    public function getDescription2()
    {
        return $this->description2;
    }

    /**
     * Set description3
     *
     * @param string $description3
     *
     * @return AdvertTranslation
     */
    public function setDescription3($description3)
    {
        $this->description3 = ucfirst($description3);

        return $this;
    }

    /**
     * Get description3
     *
     * @return string
     */
    public function getDescription3()
    {
        return $this->description3;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AdvertTranslation
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
     * @return AdvertTranslation
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
     * Set advert
     *
     * @param Advert $advert
     *
     * @return AdvertTranslation
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
     * Set title
     *
     * @param string $title
     *
     * @return AdvertTranslation
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
     * Set slug
     *
     * @param string $slug
     *
     * @return AdvertTranslation
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
}
