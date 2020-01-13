<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * VouchersUsers
 *
 * @ORM\Table(name="vouchers_users")
 * @ORM\Entity
 */
class VouchersUsers
{
    /**
     * @var string
     *
     * @ORM\Column(name="vouchers_users_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var Voucher $voucher
     *
     * @ORM\ManyToOne(
     *   targetEntity="Voucher",
     *   inversedBy="users",
     *   cascade={"persist", "merge"},
     *   fetch="LAZY"
     * )
     * @ORM\JoinColumn(name="voucher_id", referencedColumnName="voucher_id", onDelete="CASCADE")
     * @JMS\Groups({})
     *
     */
    private $voucher;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(
     *   targetEntity="User",
     *   inversedBy="vouchers",
     *   cascade={"persist", "merge"},
     *   fetch="LAZY"
     * )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     * @JMS\Groups({})
     */
    private $user;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getVoucher(): ?Voucher
    {
        return $this->voucher;
    }

    public function setVoucher(?Voucher $voucher): self
    {
        $this->voucher = $voucher;

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
