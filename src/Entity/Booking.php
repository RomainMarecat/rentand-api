<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Booking
 *
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @var int
     *
     * @ORM\Column(name="booking_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="wallet_id", type="string", length=255, unique=true, nullable=true)
     */
    private $walletId;

    /**
     * @var string statut booking done or canceled
     *
     * @ORM\Column(name="statut", type="integer")
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    private $statut;

    /**
     * @var string mangoPayTransactionId
     *
     * @ORM\Column(name="mango_pay_transaction_id", type="string", length=255, nullable=true)
     */
    private $mangoPayTransactionId;

    /**
     * @var string voucher transaction
     *
     * @ORM\Column(name="transaction", type="string", length=255, nullable=true)
     */
    private $transaction;

    /**
     * @var string code sms and email auto generated
     *
     * @ORM\Column(name="code", type="string", length=80, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(name="cancellation", type="integer", nullable=true)
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    private $cancellation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="SET NULL")
     * @JMS\Groups({"", "getMyBookings", "getBookings", "getBooking", "patchBooking", "putBooking",
     *     "adminGetBookings", "adminGetBooking"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="coach_id", referencedColumnName="user_id", onDelete="SET NULL")
     * @JMS\Groups({"", "getMyBookings", "getBookings", "getBooking", "patchBooking", "putBooking",
     *     "adminGetBookings"})
     */
    private $coach;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="booking", cascade={"remove"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Groups({"", "getUsers", "getUser", "postSimpleSearch"})
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getWalletId(): ?string
    {
        return $this->walletId;
    }

    public function setWalletId(?string $walletId): self
    {
        $this->walletId = $walletId;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMangoPayTransactionId(): ?string
    {
        return $this->mangoPayTransactionId;
    }

    public function setMangoPayTransactionId(?string $mangoPayTransactionId): self
    {
        $this->mangoPayTransactionId = $mangoPayTransactionId;

        return $this;
    }

    public function getTransaction(): ?string
    {
        return $this->transaction;
    }

    public function setTransaction(?string $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCancellation(): ?int
    {
        return $this->cancellation;
    }

    public function setCancellation(?int $cancellation): self
    {
        $this->cancellation = $cancellation;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCoach(): ?User
    {
        return $this->coach;
    }

    public function setCoach(?User $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBooking($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getBooking() === $this) {
                $comment->setBooking(null);
            }
        }

        return $this;
    }
}
