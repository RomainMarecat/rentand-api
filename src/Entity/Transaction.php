<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction_transaction")
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @var string
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="initial_amount", type="float", nullable=false)
     */
    protected $initialAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="rest_amount", type="float", nullable=false)
     */
    protected $restAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="maximum_amount", type="float", nullable=true)
     */
    protected $maximumAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="minimum_amount", type="float", nullable=true)
     */
    protected $minimumAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    protected $status;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Voucher", inversedBy="transactions")
     * @ORM\JoinColumn(name="voucher_id", referencedColumnName="voucher_id", nullable=true, onDelete="SET NULL")
     * @JMS\Groups({"hidden", "postTransactions"})
     */
    protected $voucher;

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
     * @return Transaction
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
     * Set slug
     *
     * @param string $slug
     * @return Transaction
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set initialAmount
     *
     * @param string $initialAmount
     * @return Transaction
     */
    public function setInitialAmount($initialAmount)
    {
        $this->initialAmount = $initialAmount;

        return $this;
    }

    /**
     * Get initialAmount
     *
     * @return string
     */
    public function getInitialAmount()
    {
        return $this->initialAmount;
    }

    /**
     * Set maximumAmount
     *
     * @param float $maximumAmount
     * @return Transaction
     */
    public function setMaximumAmount($maximumAmount)
    {
        $this->maximumAmount = $maximumAmount;

        return $this;
    }

    /**
     * Get maximumAmount
     *
     * @return float
     */
    public function getMaximumAmount()
    {
        return $this->maximumAmount;
    }

    /**
     * Set minimumAmount
     *
     * @param float $minimumAmount
     * @return Transaction
     */
    public function setMinimumAmount($minimumAmount)
    {
        $this->minimumAmount = $minimumAmount;

        return $this;
    }

    /**
     * Get minimumAmount
     *
     * @return float
     */
    public function getMinimumAmount()
    {
        return $this->minimumAmount;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Transaction
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set voucher
     *
     * @param \App\Entity\Voucher $voucher
     * @return Transaction
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Transaction
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
     * Set restAmount
     *
     * @param float $restAmount
     *
     * @return Transaction
     */
    public function setRestAmount($restAmount)
    {
        $this->restAmount = $restAmount;

        return $this;
    }

    /**
     * Get restAmount
     *
     * @return float
     */
    public function getRestAmount()
    {
        return $this->restAmount;
    }
}
