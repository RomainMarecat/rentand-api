<?php

namespace App\Entity;

use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cart
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Groups({"cart", "delivery", "deliveries"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @JMS\Groups({"cart"})
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     * @JMS\Groups({"cart"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"cart"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"cart"})
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", mappedBy="cart", cascade={"persist"})
     * @JMS\Groups({"cart"})
     */
    private $items;

    /**
     * @ORM\Column(type="float")
     * @JMS\Groups({"cart"})
     */
    private $fees;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order", mappedBy="cart", cascade={"persist", "remove"})
     * @JMS\Groups({"cart"})
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Delivery", inversedBy="carts", cascade={"persist"})
     * @JMS\Groups({"cart"})
     */
    private $delivery;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection|CartItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CartItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCart($this);
        }

        return $this;
    }

    public function removeItem(CartItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }

    public function getFees(): ?float
    {
        return $this->fees;
    }

    public function setFees(float $fees): self
    {
        $this->fees = $fees;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        // set the owning side of the relation if necessary
        if ($order->getCart() !== $this) {
            $order->setCart($this);
        }

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }
}
