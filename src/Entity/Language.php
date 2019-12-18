<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LanguageRepository")
 */
class Language
{
    /**
     * @var string
     *
     * @ORM\Column(name="lanaguage_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(name="ISO639_1", type="string", length=255)
     */
    private $ISO6391;

    /**
     * @ORM\Column(name="ISO639_2", type="string", length=255)
     */
    private $ISO6392;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="translations", type="json", nullable=true)
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="languages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    public function getId(): ?int
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

    public function getTranslation()
    {
        return $this->translation;
    }

    public function setTranslation($translation): self
    {
        $this->translation = $translation;

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
}
