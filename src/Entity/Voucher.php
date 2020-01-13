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
 * @ORM\Table(name="voucher")
 * @ORM\Entity
 */
class Voucher
{
    /**
     * @var string
     *
     * @ORM\Column(name="voucher_id", type="guid")
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false, unique=true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="cash_available", type="float", nullable=false)
     */
    private $cashAvailable;

    /**
     * @var float
     *
     * @ORM\Column(name="cash_reserved", type="float", nullable=false)
     */
    private $cashReserved;

    /**
     * @var float
     *
     * @ORM\Column(name="cash_used", type="float", nullable=false)
     */
    private $cashUsed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
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
     * @ORM\ManyToOne(targetEntity="Plan", inversedBy="vouchers", cascade={"persist", "remove"}, fetch="LAZY")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="plan_id", nullable=true, onDelete="CASCADE")
     * @JMS\Groups({})
     */
    private $plan;

    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="voucher", fetch="EXTRA_LAZY")
     * @JMS\Groups({"postSearchVouchers"})
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="Import", mappedBy="voucher", fetch="EXTRA_LAZY")
     * @JMS\Groups({})
     */
    private $imports;

    /**
     * @var ArrayCollection Users $users
     *
     * @ORM\OneToMany(targetEntity="App\Entity\VouchersUsers", mappedBy="voucher", cascade={"persist", "merge"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="voucher_id", referencedColumnName="voucher_id")
     * @JMS\Groups({})
     */
    private $users;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->imports = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCashAvailable(): ?float
    {
        return $this->cashAvailable;
    }

    public function setCashAvailable(float $cashAvailable): self
    {
        $this->cashAvailable = $cashAvailable;

        return $this;
    }

    public function getCashReserved(): ?float
    {
        return $this->cashReserved;
    }

    public function setCashReserved(float $cashReserved): self
    {
        $this->cashReserved = $cashReserved;

        return $this;
    }

    public function getCashUsed(): ?float
    {
        return $this->cashUsed;
    }

    public function setCashUsed(float $cashUsed): self
    {
        $this->cashUsed = $cashUsed;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setVoucher($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getVoucher() === $this) {
                $transaction->setVoucher(null);
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
            $import->setVoucher($this);
        }

        return $this;
    }

    public function removeImport(Import $import): self
    {
        if ($this->imports->contains($import)) {
            $this->imports->removeElement($import);
            // set the owning side to null (unless already changed)
            if ($import->getVoucher() === $this) {
                $import->setVoucher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VouchersUsers[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(VouchersUsers $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setVoucher($this);
        }

        return $this;
    }

    public function removeUser(VouchersUsers $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getVoucher() === $this) {
                $user->setVoucher(null);
            }
        }

        return $this;
    }
}
