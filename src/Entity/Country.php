<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @JMS\ExclusionPolicy("none")
 */
class Country
{
    /**
     * @ORM\Column(name="country_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getCountries", "patchUsers", "getAccount"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"getCountries", "patchUsers", "getAccount"})
     */
    private $name;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @JMS\Groups({"getCountries"})
     */
    private $latlng;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @JMS\Groups({"getCountries"})
     */
    private $timezones = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"getCountries", "patchUsers", "getAccount"})
     */
    private $alpha2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"getCountries", "patchUsers", "getAccount"})
     */
    private $alpha3;

    /**
     * @ORM\Column(name="calling_codes", type="array")
     * @JMS\Groups({"getCountries"})
     */
    private $callingCodes = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"getCountries"})
     */
    private $numericCode;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @JMS\Groups({"getCountries", "patchUsers", "getAccount"})
     */
    private $translations;

    /**
     * @ORM\Column(type="object", nullable=true)
     * @JMS\Groups({"getCountries", "patchUsers", "getAccount"})
     */
    private $demonym;

    /**
     * @ORM\Column(type="object", nullable=true)
     * @JMS\Groups({"getCountries"})
     */
    private $currencies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Language", mappedBy="country", orphanRemoval=true)
     */
    private $languages;

    public function __construct()
    {
        $this->languages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getTimezones(): ?array
    {
        return $this->timezones;
    }

    public function setTimezones(?array $timezones): self
    {
        $this->timezones = $timezones;

        return $this;
    }

    public function getAlpha2(): ?string
    {
        return $this->alpha2;
    }

    public function setAlpha2(string $alpha2): self
    {
        $this->alpha2 = $alpha2;

        return $this;
    }

    public function getAlpha3(): ?string
    {
        return $this->alpha3;
    }

    public function setAlpha3(?string $alpha3): self
    {
        $this->alpha3 = $alpha3;

        return $this;
    }

    public function getCallingCodes(): ?array
    {
        return $this->callingCodes;
    }

    public function setCallingCodes(array $callingCodes): self
    {
        $this->callingCodes = $callingCodes;

        return $this;
    }

    public function getNumericCode(): ?string
    {
        return $this->numericCode;
    }

    public function setNumericCode(?string $numericCode): self
    {
        $this->numericCode = $numericCode;

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

    public function getDemonym()
    {
        return $this->demonym;
    }

    public function setDemonym($demonym): self
    {
        $this->demonym = $demonym;

        return $this;
    }

    public function getCurrencies()
    {
        return $this->currencies;
    }

    public function setCurrencies($currencies): self
    {
        $this->currencies = $currencies;

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages[] = $language;
            $language->setCountry($this);
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        if ($this->languages->contains($language)) {
            $this->languages->removeElement($language);
            // set the owning side to null (unless already changed)
            if ($language->getCountry() === $this) {
                $language->setCountry(null);
            }
        }

        return $this;
    }

    public function getTranslations(): ?array
    {
        return $this->translations;
    }

    public function setTranslations(?array $translations): self
    {
        $this->translations = $translations;

        return $this;
    }

    public function getLatlng(): ?array
    {
        return $this->latlng;
    }

    public function setLatlng(?array $latlng): self
    {
        $this->latlng = $latlng;

        return $this;
    }
}
