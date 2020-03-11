<?php

namespace App\Entity;

use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeliveryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Delivery
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Groups({"delivery", "deliveries", "cart", "order"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cart", mappedBy="delivery")
     * @JMS\Groups({"delivery", "deliveries", "cart", "order"})
     */
    private $carts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="address_id", nullable=false)
     * @JMS\Groups({"delivery", "deliveries", "cart", "order"})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="address_id", nullable=true)
     * @JMS\Groups({"delivery", "deliveries", "cart", "order"})
     */
    private $billing;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="delivery", orphanRemoval=true)
     * @JMS\Groups({"delivery", "deliveries", "cart", "order"})
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     * @JMS\Groups({"delivery", "deliveries", "cart", "order"})
     */
    private $user;

    public function __construct()
    {
        $this->carts = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setDelivery($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->contains($cart)) {
            $this->carts->removeElement($cart);
            // set the owning side to null (unless already changed)
            if ($cart->getDelivery() === $this) {
                $cart->setDelivery(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getBilling(): ?Address
    {
        return $this->billing;
    }

    public function setBilling(?Address $billing): self
    {
        $this->billing = $billing;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setDelivery($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getDelivery() === $this) {
                $order->setDelivery(null);
            }
        }

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
