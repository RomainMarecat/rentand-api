<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 */
class Token
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $object;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientIp;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $used;

    /**
     * @ORM\Column(type="boolean")
     */
    private $livemode;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Card", inversedBy="token", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $card;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", mappedBy="token", cascade={"persist", "remove"})
     */
    private $payment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    public function setClientIp(?string $clientIp): self
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(?int $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(?bool $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getLivemode(): ?bool
    {
        return $this->livemode;
    }

    public function setLivemode(bool $livemode): self
    {
        $this->livemode = $livemode;

        return $this;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): self
    {
        $this->payment = $payment;

        // set the owning side of the relation if necessary
        if ($payment->getToken() !== $this) {
            $payment->setToken($this);
        }

        return $this;
    }
}
