<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\MappedSuperclass()
 */
class Event
{
    /**
     * @var string
     *
     * @ORM\Column(name="event_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getSessionsByUser"})
     */
    protected $id;

    /**
     * @ORM\Column(name="custom_title", type="string", length=255, nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    protected $customTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    protected $comment;

    /**
     * @ORM\Column(name="group_booking", type="string", length=255, nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    protected $groupBooking;

    /**
     * @ORM\Column(name="start", type="datetime")
     * @JMS\Groups({"getSessionsByUser"})
     * @JMS\Type("DateTime<'Y-m-d h:i:s'>")
     */
    protected $start;

    /**
     * @ORM\Column(name="end", type="datetime")
     * @JMS\Groups({"getSessionsByUser"})
     * @JMS\Type("DateTime<'Y-m-d h:i:s'>")
     */
    protected $end;

    /**
     * @ORM\Column(name="details", type="object", nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    protected $details;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @JMS\Groups({"getSessionsByUser"})
     */
    protected $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"getSessionsByUser"})
     */
    protected $pause;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCustomTitle(): ?string
    {
        return $this->customTitle;
    }

    public function setCustomTitle(?string $customTitle): self
    {
        $this->customTitle = $customTitle;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getGroupBooking(): ?string
    {
        return $this->groupBooking;
    }

    public function setGroupBooking(?string $groupBooking): self
    {
        $this->groupBooking = $groupBooking;

        return $this;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setDetails($details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPause(): ?int
    {
        return $this->pause;
    }

    public function setPause(?int $pause): self
    {
        $this->pause = $pause;

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
}
