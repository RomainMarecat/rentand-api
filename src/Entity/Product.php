<?php

namespace App\Entity;

use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Groups({"product", "cart"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"product", "cart"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @JMS\Groups({"product", "cart"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"product", "cart"})
     */
    private $keywords;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"product", "cart"})
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"product", "cart"})
     */
    private $alias;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"product", "cart"})
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="json")
     */
    private $translations;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @JMS\Groups({"product", "cart"})
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @JMS\Groups({"product", "cart"})
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="media_id")
     * @JMS\Groups({"product", "cart"})
     */
    private $media;

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("translations")
     * @JMS\Groups({"product", "cart"})
     */
    public function getTranslationsObject()
    {
        return $this->getTranslations();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTranslations(): ?array
    {
        return $this->translations;
    }

    public function setTranslations(array $translations): self
    {
        $this->translations = $translations;

        return $this;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(?array $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }
}
