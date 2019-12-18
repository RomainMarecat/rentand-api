<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * VouchersUsers
 *
 * @ORM\Table(name="vouchers_users")
 * @ORM\Entity
 */
class VouchersUsers
{
    /**
     * @var int
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
     * @var App\Entity\Voucher $voucher
     *
     * @ORM\ManyToOne(
     *   targetEntity="App\Entity\Voucher",
     *   inversedBy="users",
     *   cascade={"persist", "merge"},
     *   fetch="LAZY"
     * )
     * @ORM\JoinColumn(name="voucher_id", referencedColumnName="voucher_id", onDelete="CASCADE")
     * @JMS\Groups({"hidden"})
     *
     */
    protected $voucher;

    /**
     * @var Entity\User $user
     *
     * @ORM\ManyToOne(
     *   targetEntity="App\Entity\User",
     *   inversedBy="vouchers",
     *   cascade={"persist", "merge"},
     *   fetch="LAZY"
     * )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     * @JMS\Groups({"hidden"})
     */
    protected $user;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return VouchersUsers
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
     * Set voucher
     *
     * @param \App\Entity\Voucher $voucher
     *
     * @return VouchersUsers
     */
    public function setVoucher(\App\Entity\Voucher $voucher = null)
    {
        $this->voucher = $voucher;

        return $this;
    }

    /**
     * Get voucher
     *
     * @return \App\Entity\Voucher
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return VouchersUsers
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
