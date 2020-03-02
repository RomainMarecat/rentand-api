<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @param \DateTime $createdAt
     * @return TimestampableTrait
     * @throws \Exception
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        if (!$createdAt) {
            $createdAt = new \DateTime();
        }

        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     * @return TimestampableTrait
     * @throws \Exception
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        if (!$updatedAt) {
            $updatedAt = new \DateTime();
        }

        $this->updatedAt = $updatedAt;


        return $this;
    }
}
