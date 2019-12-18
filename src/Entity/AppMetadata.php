<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AppMetadataRepository")
 */
class AppMetadata
{
    /**
     * @var string
     *
     * @ORM\Column(name="app_metadata_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"Default", "getUsers"})
     */
    private $id;

    /**
     * @ORM\Column(name="admin", type="boolean")
     */
    private $admin;

    /**
     * @ORM\Column(name="coach", type="boolean")
     */
    private $coach;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="appMetadata")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getCoach(): ?bool
    {
        return $this->coach;
    }

    public function setCoach(bool $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
