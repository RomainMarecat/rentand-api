<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Sport
 *
 * @ORM\Table(name="sport")
 * @ORM\Entity(repositoryClass="App\Repository\SportRepository")
 * @JMS\ExclusionPolicy("none")
 */
class Sport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="sport_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getUser", "getSportsTeachedByUser", "addSession", "getSports"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @JMS\Groups({"getUser", "getSportsTeachedByUser", "addSession", "getSports"})
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, separator="-", updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     * @JMS\Groups({"getUser", "addSession", "getSports"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     * @JMS\Groups({"getUser", "addSession"})
     */
    private $level;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="translations", type="json")
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="sport_id", onDelete="CASCADE")
     * @JMS\Groups({})
     * @JMS\MaxDepth(2)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="SportTeached", mappedBy="sport", fetch="EXTRA_LAZY")
     * @JMS\Exclude
     * @JMS\Groups({})
     */
    private $sportsTeached;

    /**
     * @ORM\ManyToMany(targetEntity="SportTeached", mappedBy="specialities")
     * @JMS\Exclude
     * @JMS\Groups({"getFamiliesByParent"})
     * @JMS\MaxDepth(1)
     */
    private $sportTeachedSpecialites;

    /**
     * @ORM\OneToMany(targetEntity="Sport", mappedBy="parent", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"getFormUser", "getSport", "getSports"})
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity="Family", mappedBy="sports", fetch="EXTRA_LAZY")
     * @JMS\Exclude
     * @JMS\Groups({})
     */
    private $families;

    /**
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="media_id", onDelete="CASCADE"))
     * @JMS\Groups({"getSport", "getSports"})
     */
    private $media;

    public function __construct()
    {
        $this->sportsTeached = new ArrayCollection();
        $this->sportTeachedSpecialites = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->families = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|SportTeached[]
     */
    public function getSportsTeached(): Collection
    {
        return $this->sportsTeached;
    }

    public function addSportsTeached(SportTeached $sportsTeached): self
    {
        if (!$this->sportsTeached->contains($sportsTeached)) {
            $this->sportsTeached[] = $sportsTeached;
            $sportsTeached->setSport($this);
        }

        return $this;
    }

    public function removeSportsTeached(SportTeached $sportsTeached): self
    {
        if ($this->sportsTeached->contains($sportsTeached)) {
            $this->sportsTeached->removeElement($sportsTeached);
            // set the owning side to null (unless already changed)
            if ($sportsTeached->getSport() === $this) {
                $sportsTeached->setSport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SportTeached[]
     */
    public function getSportTeachedSpecialites(): Collection
    {
        return $this->sportTeachedSpecialites;
    }

    public function addSportTeachedSpecialite(SportTeached $sportTeachedSpecialite): self
    {
        if (!$this->sportTeachedSpecialites->contains($sportTeachedSpecialite)) {
            $this->sportTeachedSpecialites[] = $sportTeachedSpecialite;
            $sportTeachedSpecialite->addSpeciality($this);
        }

        return $this;
    }

    public function removeSportTeachedSpecialite(SportTeached $sportTeachedSpecialite): self
    {
        if ($this->sportTeachedSpecialites->contains($sportTeachedSpecialite)) {
            $this->sportTeachedSpecialites->removeElement($sportTeachedSpecialite);
            $sportTeachedSpecialite->removeSpeciality($this);
        }

        return $this;
    }

    /**
     * @return Collection|Sport[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Sport $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Sport $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Family[]
     */
    public function getFamilies(): Collection
    {
        return $this->families;
    }

    public function addFamily(Family $family): self
    {
        if (!$this->families->contains($family)) {
            $this->families[] = $family;
            $family->addSport($this);
        }

        return $this;
    }

    public function removeFamily(Family $family): self
    {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
            $family->removeSport($this);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslations()
    {
        return json_decode($this->translations, true);
    }

    /**
     * Get translations
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("translations")
     * @JMS\Groups({"Default", "getUsers", "getSports", "getSport", "getFormUser", "getFormSearch", "getFormSport",
     *     "getFormFamily",
     *     "getUser", "getFormUser", "getFamiliesByParent", "getSimpleSearch", "postSimpleSearch",
     *     "postAdvancedSearch", "getAdvancedSearch", "newPreBookings", "getSport", "getUserSportById", "addSession"})
     *
     */
    public function getTranslationsObject()
    {
        return $this->getTranslations();
    }

    public function setTranslations($translations): self
    {
        $this->translations = $translations;

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
