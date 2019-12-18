<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Structure
 *
 * @ORM\Table(name="structure_link")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("none")
 */
class StructureLink
{
    /**
     * @var string
     *
     * @ORM\Column(name="structure_link_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="structureLinks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     * @var Structure
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="structureLinks")
     * @ORM\JoinColumn(name="structure_id", referencedColumnName="structure_id")
     */
    private $structure;

    /**
     * @var integer
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return StructureLink
     */
    public function setId(int $id): StructureLink
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return StructureLink
     */
    public function setUser(User $user): StructureLink
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Structure
     */
    public function getStructure(): Structure
    {
        return $this->structure;
    }

    /**
     * @param Structure $structure
     *
     * @return StructureLink
     */
    public function setStructure(Structure $structure): StructureLink
    {
        $this->structure = $structure;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return StructureLink
     */
    public function setStatus(int $status): StructureLink
    {
        $this->status = $status;
        return $this;
    }
}
