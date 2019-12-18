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
 * @ORM\Table(name="structure_structure")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("none")
 */
class Structure
{
    /**
     * @var string
     *
     * @ORM\Column(name="structure_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @var ArrayCollection
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
     * @JMS\Groups({"hidden"})
     */
    private $structureLinks;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->structureLinks = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Structure
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Structure
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Structure
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Structure
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
     * @return Structure
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
