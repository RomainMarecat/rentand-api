<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Voucher
 *
 * @ORM\Table(name="voucher_voucher")
 * @ORM\Entity
 */
class Voucher
{
    /**
     * @var integer
     *
     * @ORM\Column(name="voucher_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
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
     * @ORM\Column(name="code", type="string", length=255, nullable=false, unique=true)
     */
    protected $code;

    /**
     * @var float
     *
     * @ORM\Column(name="cash_available", type="float", nullable=false)
     */
    protected $cashAvailable;

    /**
     * @var float
     *
     * @ORM\Column(name="cash_reserved", type="float", nullable=false)
     */
    protected $cashReserved;

    /**
     * @var float
     *
     * @ORM\Column(name="cash_used", type="float", nullable=false)
     */
    protected $cashUsed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime", nullable=false)
     */
    protected $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=false)
     */
    protected $end;

    /**
     * @ORM\ManyToOne(targetEntity="Plan", inversedBy="vouchers", cascade={"persist", "remove"}, fetch="LAZY")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="plan_id", nullable=true, onDelete="CASCADE")
     * @JMS\Groups({"hidden"})
     */
    protected $plan;

    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="voucher", fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden", "postSearchVouchers"})
     */
    protected $transactions;

    /**
     * @ORM\OneToMany(targetEntity="Import", mappedBy="voucher", fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden"})
     */
    protected $imports;

    /**
     * @var ArrayCollection Users $users
     *
     * @ORM\OneToMany(targetEntity="App\Entity\VouchersUsers", mappedBy="voucher", cascade={"persist", "merge"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="voucher_id", referencedColumnName="voucher_id")
     * @JMS\Groups({"hidden"})
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->imports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->users = new ArrayCollection();
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
     * @return Voucher
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
     * @return Voucher
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
     * Set code
     *
     * @param string $code
     * @return Voucher
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
     * Set cashAvailable
     *
     * @param float $cashAvailable
     * @return Voucher
     */
    public function setCashAvailable($cashAvailable)
    {
        $this->cashAvailable = $cashAvailable;

        return $this;
    }

    /**
     * Get cashAvailable
     *
     * @return float
     */
    public function getCashAvailable()
    {
        return $this->cashAvailable;
    }

    /**
     * Set cashReserved
     *
     * @param float $cashReserved
     * @return Voucher
     */
    public function setCashReserved($cashReserved)
    {
        $this->cashReserved = $cashReserved;

        return $this;
    }

    /**
     * Get cashReserved
     *
     * @return float
     */
    public function getCashReserved()
    {
        return $this->cashReserved;
    }

    /**
     * Set cashUsed
     *
     * @param float $cashUsed
     * @return Voucher
     */
    public function setCashUsed($cashUsed)
    {
        $this->cashUsed = $cashUsed;

        return $this;
    }

    /**
     * Get cashUsed
     *
     * @return float
     */
    public function getCashUsed()
    {
        return $this->cashUsed;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Voucher
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Voucher
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
     * @return Voucher
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
     * Set start
     *
     * @param \DateTime $start
     * @return Voucher
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Voucher
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set plan
     *
     * @param \App\Entity\Plan $plan
     * @return Voucher
     */
    public function setPlan(\App\Entity\Plan $plan = null)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return \App\Entity\Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Add transactions
     *
     * @param \App\Entity\Transaction $transactions
     * @return Voucher
     */
    public function addTransaction(\App\Entity\Transaction $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param \App\Entity\Transaction $transactions
     */
    public function removeTransaction(\App\Entity\Transaction $transactions)
    {
        $this->transactions->removeElement($transactions);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Add imports
     *
     * @param \App\Entity\Import $imports
     * @return Voucher
     */
    public function addImport(\App\Entity\Import $imports)
    {
        $this->imports[] = $imports;

        return $this;
    }

    /**
     * Remove imports
     *
     * @param \App\Entity\Import $imports
     */
    public function removeImport(\App\Entity\Import $imports)
    {
        $this->imports->removeElement($imports);
    }

    /**
     * Get imports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImports()
    {
        return $this->imports;
    }

    /**
     * Add user
     *
     * @param User $user
     *
     * @return Voucher
     */
    public function addUser(User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
