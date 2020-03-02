<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 */
class Card
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
    private $addressCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine1Check;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressState;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressZip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressZipCheck;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cvcCheck;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dynamicLast4;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $expMonth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $expYear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $funding;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metadata;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tokenizationMethod;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", mappedBy="card", cascade={"persist", "remove"})
     */
    private $payment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Token", mappedBy="card", cascade={"persist", "remove"})
     */
    private $token;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function setAddressCity(?string $addressCity): self
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    public function getAddressCountry(): ?string
    {
        return $this->addressCountry;
    }

    public function setAddressCountry(?string $addressCountry): self
    {
        $this->addressCountry = $addressCountry;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine1Check(): ?string
    {
        return $this->addressLine1Check;
    }

    public function setAddressLine1Check(?string $addressLine1Check): self
    {
        $this->addressLine1Check = $addressLine1Check;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getAddressState(): ?string
    {
        return $this->addressState;
    }

    public function setAddressState(?string $addressState): self
    {
        $this->addressState = $addressState;

        return $this;
    }

    public function getAddressZip(): ?string
    {
        return $this->addressZip;
    }

    public function setAddressZip(?string $addressZip): self
    {
        $this->addressZip = $addressZip;

        return $this;
    }

    public function getAddressZipCheck(): ?string
    {
        return $this->addressZipCheck;
    }

    public function setAddressZipCheck(?string $addressZipCheck): self
    {
        $this->addressZipCheck = $addressZipCheck;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCvcCheck(): ?string
    {
        return $this->cvcCheck;
    }

    public function setCvcCheck(?string $cvcCheck): self
    {
        $this->cvcCheck = $cvcCheck;

        return $this;
    }

    public function getDynamicLast4(): ?string
    {
        return $this->dynamicLast4;
    }

    public function setDynamicLast4(?string $dynamicLast4): self
    {
        $this->dynamicLast4 = $dynamicLast4;

        return $this;
    }

    public function getExpMonth(): ?int
    {
        return $this->expMonth;
    }

    public function setExpMonth(?int $expMonth): self
    {
        $this->expMonth = $expMonth;

        return $this;
    }

    public function getExpYear(): ?int
    {
        return $this->expYear;
    }

    public function setExpYear(?int $expYear): self
    {
        $this->expYear = $expYear;

        return $this;
    }

    public function getFunding(): ?string
    {
        return $this->funding;
    }

    public function setFunding(?string $funding): self
    {
        $this->funding = $funding;

        return $this;
    }

    public function getLast4(): ?string
    {
        return $this->last4;
    }

    public function setLast4(?string $last4): self
    {
        $this->last4 = $last4;

        return $this;
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }

    public function setMetadata(?string $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTokenizationMethod(): ?string
    {
        return $this->tokenizationMethod;
    }

    public function setTokenizationMethod(?string $tokenizationMethod): self
    {
        $this->tokenizationMethod = $tokenizationMethod;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        // set (or unset) the owning side of the relation if necessary
        $newCard = null === $payment ? null : $this;
        if ($payment->getCard() !== $newCard) {
            $payment->setCard($newCard);
        }

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(Token $token): self
    {
        $this->token = $token;

        // set the owning side of the relation if necessary
        if ($token->getCard() !== $this) {
            $token->setCard($this);
        }

        return $this;
    }
}
