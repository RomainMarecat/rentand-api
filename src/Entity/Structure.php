<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Structure
 *
 * @ORM\Table(name="structure")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("none")
 */
class Structure
{
    /**
     * @var string
     *
     * @ORM\Column(name="structure_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Accessor(getter="getName",setter="setName")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    private $code;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="structure")
     */
    private $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *    targetEntity="StructureLink",
     *    mappedBy="structure",
     *    cascade={"persist", "merge"},
     *    fetch="EXTRA_LAZY"
     * )
     * @JMS\Groups({})
     */
    private $structureLinks;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->structureLinks = new ArrayCollection();
    }

    public function getId(): ?string
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setStructure($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getStructure() === $this) {
                $user->setStructure(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StructureLink[]
     */
    public function getStructureLinks(): Collection
    {
        return $this->structureLinks;
    }

    public function addStructureLink(StructureLink $structureLink): self
    {
        if (!$this->structureLinks->contains($structureLink)) {
            $this->structureLinks[] = $structureLink;
            $structureLink->setStructure($this);
        }

        return $this;
    }

    public function removeStructureLink(StructureLink $structureLink): self
    {
        if ($this->structureLinks->contains($structureLink)) {
            $this->structureLinks->removeElement($structureLink);
            // set the owning side to null (unless already changed)
            if ($structureLink->getStructure() === $this) {
                $structureLink->setStructure(null);
            }
        }

        return $this;
    }
}
