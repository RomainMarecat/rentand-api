<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Plan
 *
 * @ORM\Table(name="plan")
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="start", type="datetime", nullable=false)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=false)
     */
    private $end;

    /**
     * @var float
     *
     * @ORM\Column(name="initial_amount", type="float", nullable=false)
     */
    private $initialAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="minimum_amount", type="float", nullable=true)
     */
    private $minimumAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="maximum_amount", type="float", nullable=true)
     */
    private $maximumAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="initial_purchase", type="float", nullable=true)
     */
    private $initialPurchase;

    /**
     * @var float
     *
     * @ORM\Column(name="minimum_purchase", type="float", nullable=true)
     */
    private $minimumPurchase;

    /**
     * @var float
     *
     * @ORM\Column(name="maximum_purchase", type="float", nullable=true)
     */
    private $maximumPurchase;

    /**
     * @var string
     * @Assert\Choice(choices = {"voucher", "discount"}, message = "Choose a valid type.")
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="limitation_number", type="integer", nullable=true)
     */
    private $limitationNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="plans", cascade={"remove"}, fetch="LAZY")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="campaign_id", nullable=true, onDelete="CASCADE")
     * @JMS\Groups({})
     */
    private $campaign;

    /**
     * @ORM\OneToMany(targetEntity="Voucher", mappedBy="plan", fetch="EXTRA_LAZY")
     * @JMS\Groups({})
     */
    private $vouchers;

    /**
     * @ORM\OneToMany(targetEntity="Import", mappedBy="plan", fetch="EXTRA_LAZY")
     * @JMS\Groups({})
     */
    private $imports;

    /**
     * Instructor relation with plans
     * @ORM\ManyToMany(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="plans_instructors",
     *      joinColumns={@ORM\JoinColumn(name="plan_id", referencedColumnName="plan_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")}
     * )
     * @JMS\Groups({})
     */
    private $instructors;

    public function __construct()
    {
        $this->vouchers = new ArrayCollection();
        $this->imports = new ArrayCollection();
        $this->instructors = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getInitialAmount(): ?float
    {
        return $this->initialAmount;
    }

    public function setInitialAmount(float $initialAmount): self
    {
        $this->initialAmount = $initialAmount;

        return $this;
    }

    public function getMinimumAmount(): ?float
    {
        return $this->minimumAmount;
    }

    public function setMinimumAmount(?float $minimumAmount): self
    {
        $this->minimumAmount = $minimumAmount;

        return $this;
    }

    public function getMaximumAmount(): ?float
    {
        return $this->maximumAmount;
    }

    public function setMaximumAmount(?float $maximumAmount): self
    {
        $this->maximumAmount = $maximumAmount;

        return $this;
    }

    public function getInitialPurchase(): ?float
    {
        return $this->initialPurchase;
    }

    public function setInitialPurchase(?float $initialPurchase): self
    {
        $this->initialPurchase = $initialPurchase;

        return $this;
    }

    public function getMinimumPurchase(): ?float
    {
        return $this->minimumPurchase;
    }

    public function setMinimumPurchase(?float $minimumPurchase): self
    {
        $this->minimumPurchase = $minimumPurchase;

        return $this;
    }

    public function getMaximumPurchase(): ?float
    {
        return $this->maximumPurchase;
    }

    public function setMaximumPurchase(?float $maximumPurchase): self
    {
        $this->maximumPurchase = $maximumPurchase;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLimitationNumber(): ?int
    {
        return $this->limitationNumber;
    }

    public function setLimitationNumber(?int $limitationNumber): self
    {
        $this->limitationNumber = $limitationNumber;

        return $this;
    }

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @return Collection|Voucher[]
     */
    public function getVouchers(): Collection
    {
        return $this->vouchers;
    }

    public function addVoucher(Voucher $voucher): self
    {
        if (!$this->vouchers->contains($voucher)) {
            $this->vouchers[] = $voucher;
            $voucher->setPlan($this);
        }

        return $this;
    }

    public function removeVoucher(Voucher $voucher): self
    {
        if ($this->vouchers->contains($voucher)) {
            $this->vouchers->removeElement($voucher);
            // set the owning side to null (unless already changed)
            if ($voucher->getPlan() === $this) {
                $voucher->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Import[]
     */
    public function getImports(): Collection
    {
        return $this->imports;
    }

    public function addImport(Import $import): self
    {
        if (!$this->imports->contains($import)) {
            $this->imports[] = $import;
            $import->setPlan($this);
        }

        return $this;
    }

    public function removeImport(Import $import): self
    {
        if ($this->imports->contains($import)) {
            $this->imports->removeElement($import);
            // set the owning side to null (unless already changed)
            if ($import->getPlan() === $this) {
                $import->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getInstructors(): Collection
    {
        return $this->instructors;
    }

    public function addInstructor(User $instructor): self
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors[] = $instructor;
        }

        return $this;
    }

    public function removeInstructor(User $instructor): self
    {
        if ($this->instructors->contains($instructor)) {
            $this->instructors->removeElement($instructor);
        }

        return $this;
    }
}
