<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table(name="image_image")
 * @ORM\Entity(repositoryClass="Repository\ImageRepository")
 */
class Image
{
    /**
     * @var integer
     *
     * @ORM\Column(name="image_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank
     */
    protected $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="ext", type="string", length=16)
     * @Assert\NotBlank
     */
    protected $ext;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    protected $alt;

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
     * @ORM\OneToOne(targetEntity="Advert", inversedBy="image")
     * @ORM\JoinColumn(name="advert_id", referencedColumnName="advert_id", onDelete="CASCADE"))
     * @JMS\Groups({"hidden"})
     */
    protected $advert;

    /**
     * @ORM\ManyToOne(targetEntity="AdvertSport", inversedBy="pictures")
     * @ORM\JoinColumn(name="advert_sport_id", referencedColumnName="advert_sport_id", onDelete="CASCADE"))
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
     * Set filename
     *
     * @param string $filename
     *
     * @return Image
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set ext
     *
     * @param string $ext
     *
     * @return Image
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Image
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
     * @return Image
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
     * @return Image
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
     * Set advertSport
     *
     * @param AdvertSport $advertSport
     *
     * @return Image
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
