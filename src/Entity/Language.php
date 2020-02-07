<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LanguageRepository")
 * @JMS\ExclusionPolicy("none")
 */
class Language
{
    /**
     * @var string
     *
     * @ORM\Column(name="language_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getLanguages", "patchUsers", "getAccount", "getUsers", "getUser"})
     */
    private $id;

    /**
     * @ORM\Column(name="ISO639_1", type="string", length=255)
     * @JMS\Groups({"getLanguages", "patchUsers", "getAccount", "getUsers", "getUser"})
     */
    private $ISO6391;

    /**
     * @ORM\Column(name="ISO639_2", type="string", length=255)
     * @JMS\Groups({"getLanguages", "patchUsers", "getAccount", "getUsers", "getUser"})
     */
    private $ISO6392;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Groups({"getLanguages", "patchUsers", "getAccount", "getUsers", "getUser"})
     */
    private $name;

    /**
     * @ORM\Column(name="translations", type="json", nullable=true)
     * @JMS\Groups({"getLanguages", "patchUsers", "getAccount", "getUsers", "getUser"})
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="languages")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=false)
     */
    private $country;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMetadata", mappedBy="languages")
     */
    private $userMetadata;

    public function __construct()
    {
        $this->userMetadata = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getISO6391(): ?string
    {
        return $this->ISO6391;
    }

    public function setISO6391(string $ISO6391): self
    {
        $this->ISO6391 = $ISO6391;

        return $this;
    }

    public function getISO6392(): ?string
    {
        return $this->ISO6392;
    }

    public function setISO6392(string $ISO6392): self
    {
        $this->ISO6392 = $ISO6392;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getTranslations()
    {
        return $this->translations;
    }

    public function setTranslations($translations): self
    {
        $this->translations = $translations;

        return $this;
    }

    /**
     * @return Collection|UserMetadata[]
     */
    public function getUserMetadata(): Collection
    {
        return $this->userMetadata;
    }

    public function addUserMetadata(UserMetadata $userMetadata): self
    {
        if (!$this->userMetadata->contains($userMetadata)) {
            $this->userMetadata[] = $userMetadata;
            $userMetadata->addLanguage($this);
        }

        return $this;
    }

    public function removeUserMetadata(UserMetadata $userMetadata): self
    {
        if ($this->userMetadata->contains($userMetadata)) {
            $this->userMetadata->removeElement($userMetadata);
            $userMetadata->removeLanguage($this);
        }

        return $this;
    }
}
