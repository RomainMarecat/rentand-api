<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Plan
 *
 * @ORM\Table(name="plan_plan")
 * @ORM\Entity(repositoryClass="App\Repository\PlanRepository")
 */
class Plan
{
    /**
     * @var integer
     *
     * @ORM\Column(name="plan_id", type="guid")
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var string
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
     * @var float
     *
     * @ORM\Column(name="initial_amount", type="float", nullable=false)
     */
    protected $initialAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="minimum_amount", type="float", nullable=true)
     */
    protected $minimumAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="maximum_amount", type="float", nullable=true)
     */
    protected $maximumAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="initial_purchase", type="float", nullable=true)
     */
    protected $initialPurchase;

    /**
     * @var float
     *
     * @ORM\Column(name="minimum_purchase", type="float", nullable=true)
     */
    protected $minimumPurchase;

    /**
     * @var float
     *
     * @ORM\Column(name="maximum_purchase", type="float", nullable=true)
     */
    protected $maximumPurchase;

    /**
     * @var string
     * @Assert\Choice(choices = {"voucher", "discount"}, message = "Choose a valid type.")
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="limitation_number", type="integer", nullable=true)
     */
    protected $limitationNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="plans", cascade={"remove"}, fetch="LAZY")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="campaign_id", nullable=true, onDelete="CASCADE")
     * @JMS\Groups({"hidden"})
     */
    protected $campaign;

    /**
     * @ORM\OneToMany(targetEntity="Voucher", mappedBy="plan", fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden"})
     */
    protected $vouchers;

    /**
     * @ORM\OneToMany(targetEntity="Import", mappedBy="plan", fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden"})
     */
    protected $imports;

    /**
     * Instructor relation with plans
     * @ORM\ManyToMany(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="plans_instructors",
     *      joinColumns={@ORM\JoinColumn(name="plan_id", referencedColumnName="plan_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")}
     * )
     * @JMS\Groups({"hidden"})
     */
    protected $instructors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->instructors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->imports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vouchers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->name;
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
     * @return Plan
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
     * @return Plan
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Plan
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
     * @return Plan
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
     * @return Plan
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
     * @return Plan
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
     * Set initialAmount
     *
     * @param float $initialAmount
     * @return Plan
     */
    public function setInitialAmount($initialAmount)
    {
        $this->initialAmount = $initialAmount;

        return $this;
    }

    /**
     * Get initialAmount
     *
     * @return float
     */
    public function getInitialAmount()
    {
        return $this->initialAmount;
    }

    /**
     * Set minimumAmount
     *
     * @param float $minimumAmount
     * @return Plan
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
     * Set maximumAmount
     *
     * @param float $maximumAmount
     * @return Plan
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
     * Set initialPurchase
     *
     * @param float $initialPurchase
     * @return Plan
     */
    public function setInitialPurchase($initialPurchase)
    {
        $this->initialPurchase = $initialPurchase;

        return $this;
    }

    /**
     * Get initialPurchase
     *
     * @return float
     */
    public function getInitialPurchase()
    {
        return $this->initialPurchase;
    }

    /**
     * Set minimumPurchase
     *
     * @param float $minimumPurchase
     * @return Plan
     */
    public function setMinimumPurchase($minimumPurchase)
    {
        $this->minimumPurchase = $minimumPurchase;

        return $this;
    }

    /**
     * Get minimumPurchase
     *
     * @return float
     */
    public function getMinimumPurchase()
    {
        return $this->minimumPurchase;
    }

    /**
     * Set maximumPurchase
     *
     * @param float $maximumPurchase
     * @return Plan
     */
    public function setMaximumPurchase($maximumPurchase)
    {
        $this->maximumPurchase = $maximumPurchase;

        return $this;
    }

    /**
     * Get maximumPurchase
     *
     * @return float
     */
    public function getMaximumPurchase()
    {
        return $this->maximumPurchase;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Plan
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set limitationNumber
     *
     * @param integer $limitationNumber
     * @return Plan
     */
    public function setLimitationNumber($limitationNumber)
    {
        $this->limitationNumber = $limitationNumber;

        return $this;
    }

    /**
     * Get limitationNumber
     *
     * @return integer
     */
    public function getLimitationNumber()
    {
        return $this->limitationNumber;
    }

    /**
     * Set campaign
     *
     * @param \App\Entity\Campaign $campaign
     * @return Plan
     */
    public function setCampaign(\App\Entity\Campaign $campaign = null)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \App\Entity\Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Add vouchers
     *
     * @param \App\Entity\Voucher $vouchers
     * @return Plan
     */
    public function addVoucher(\App\Entity\Voucher $vouchers)
    {
        $this->vouchers[] = $vouchers;

        return $this;
    }

    /**
     * Remove vouchers
     *
     * @param \App\Entity\Voucher $vouchers
     */
    public function removeVoucher(\App\Entity\Voucher $vouchers)
    {
        $this->vouchers->removeElement($vouchers);
    }

    /**
     * Get vouchers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVouchers()
    {
        return $this->vouchers;
    }

    /**
     * Add imports
     *
     * @param \App\Entity\Import $imports
     * @return Plan
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
}
