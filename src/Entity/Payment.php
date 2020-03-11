<?php

namespace App\Entity;

use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Payment
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Groups({"order", "orders"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order", inversedBy="payment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"order", "orders"})
     */
    private $order;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Token", inversedBy="payment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"order", "orders"})
     */
    private $token;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(Token $token): self
    {
        $this->token = $token;

        return $this;
    }
}
