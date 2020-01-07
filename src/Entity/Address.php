<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Table(name="address_address")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("none")
 */
class Address
{
    /**
     * @var integer
     *
     * @ORM\Column(name="address_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getAccount", "patchUsers"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     * @JMS\Groups({"getAccount", "patchUsers"})
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     * @JMS\Groups({"getAccount", "patchUsers"})
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @JMS\Groups({"getAccount", "patchUsers"})
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country", referencedColumnName="country_id", nullable=true)
     * @Assert\NotBlank
     * @JMS\Groups({"getAccount", "patchUsers"})
     */
    private $country;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     *
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @JMS\Exclude
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="UserMetadata", inversedBy="address")
     * @ORM\JoinColumn(name="user_metadata_id", referencedColumnName="user_metadata_id", onDelete="CASCADE")
     * @JMS\Exclude
     * @JMS\Groups({"hidden"})
     */
    private $userMetadata;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getUserMetadata(): ?UserMetadata
    {
        return $this->userMetadata;
    }

    public function setUserMetadata(?UserMetadata $userMetadata): self
    {
        $this->userMetadata = $userMetadata;

        return $this;
    }
}
