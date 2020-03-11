<?php

namespace App\Entity;

use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CartItem
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Groups({"cart"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cart", inversedBy="items")
     */
    private $cart;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"cart"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"cart"})
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Groups({"cart"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     * @JMS\Groups({"cart"})
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"cart"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"cart"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"cart"})
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Session", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="event_id", referencedColumnName="event_id", nullable=true)
     * @JMS\Groups({"cart"})
     */
    private $session;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }
}
