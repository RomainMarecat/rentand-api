<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Family
 *
 * @ORM\Table(name="family")
 * @ORM\Entity(repositoryClass="App\Repository\FamilyRepository")
 * @JMS\ExclusionPolicy("none")
 */
class Family
{
    /**
     * @var string
     *
     * @ORM\Column(name="family_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, separator="-", updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="Family", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="family_id", onDelete="CASCADE")
     * @JMS\Groups({"getFormFamily", "getFamily"})
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Family", mappedBy="parent", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"getFamiliesByParent"})
     */
    private $children;

    /**
     * @JMS\Type("ArrayCollection<App\Entity\Sport>")
     * @ORM\ManyToMany(targetEntity="App\Entity\Sport", inversedBy="families", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="families_sports",
     *      joinColumns={
     *          @ORM\JoinColumn(name="family_id", referencedColumnName="family_id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     *      }
     * )
     * @JMS\Groups({"getFormFamily", "getFamily"})
     */
    private $sports;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->sports = new ArrayCollection();
    }

    public function getId(): ?string
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

    public function getTranslations(): ?array
    {
        return $this->translations;
    }

    public function setTranslations(array $translations): self
    {
        $this->translations = $translations;

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
     * @return Collection|Family[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Family $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Family $child): self
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
     * @return Collection|Sport[]
     */
    public function getSports(): Collection
    {
        return $this->sports;
    }

    public function addSport(Sport $sport): self
    {
        if (!$this->sports->contains($sport)) {
            $this->sports[] = $sport;
        }

        return $this;
    }

    public function removeSport(Sport $sport): self
    {
        if ($this->sports->contains($sport)) {
            $this->sports->removeElement($sport);
        }

        return $this;
    }
}
