<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    /**
     * @var string
     *
     * @ORM\Column(name="media_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"Default", "getUsers", "getUser", "getSport", "getSports"})
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=false)
     * @JMS\Groups({"Default", "getUsers", "getUser", "getSport", "getSports"})
     * @Assert\NotBlank
     */
    private $filename;


    /**
     * @var string
     *
     * @ORM\Column(name="ext", type="string", length=16)
     * @Assert\NotBlank
     */
    private $ext;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     * @JMS\Groups({"getUsers", "getUser", "getSport", "getSports"})
     */
    private $alt;


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
     * @ORM\ManyToOne(targetEntity="SportTeached", inversedBy="pictures")
     * @ORM\JoinColumn(name="sport_teached_id", referencedColumnName="sport_teached_id", onDelete="CASCADE"))
     * @JMS\Groups({})
     */
    private $sportTeached;


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
     * @return Media
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
     * @return Media
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
     * @return Media
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
     * @return Media
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
     * @return Media
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
     * @return string
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("url")
     * @JMS\Groups({"Default", "getUsers", "getUser", "getSport", "getSports"})
     */
    public function getUrl(): ?string
    {
        if (empty($this->filename)) {
            return '/assets/images/picto-fusee.png';
        }
        return str_replace(
            '/home/romainmarecat/alr/rentand-api/public',
            'http://localhost:8001',
            $this->filename
        );
    }

    public function getSportTeached(): ?SportTeached
    {
        return $this->sportTeached;
    }

    public function setSportTeached(?SportTeached $sportTeached): self
    {
        $this->sportTeached = $sportTeached;

        return $this;
    }
}
