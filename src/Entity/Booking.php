<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Booking
 *
 * @ORM\Table(name="booking_booking")
 * @ORM\Entity(repositoryClass="Repository\BookingRepository")
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
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="wallet_id", type="string", length=255, unique=true, nullable=true)
     */
    protected $walletId;

    /**
     * @var string statut booking done or canceled
     *
     * @ORM\Column(name="statut", type="integer")
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    protected $statut;

    /**
     * @var string mangoPayTransactionId
     *
     * @ORM\Column(name="mango_pay_transaction_id", type="string", length=255, nullable=true)
     */
    protected $mangoPayTransactionId;

    /**
     * @var string voucher transaction
     *
     * @ORM\Column(name="transaction", type="string", length=255, nullable=true)
     */
    protected $transaction;

    /**
     * @var string code sms and email auto generated
     *
     * @ORM\Column(name="code", type="string", length=80, nullable=true)
     */
    protected $code;

    /**
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    protected $price;

    /**
     * @ORM\Column(name="cancellation", type="integer", nullable=true)
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    protected $cancellation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"Default", "adminGetBookings"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="SET NULL")
     * @JMS\Groups({"hidden", "getMyBookings", "getBookings", "getBooking", "patchBooking", "putBooking", "adminGetBookings", "adminGetBooking"})
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Advert", inversedBy="bookings")
     * @ORM\JoinColumn(name="advert_id", referencedColumnName="advert_id", onDelete="SET NULL")
     * @JMS\Groups({"hidden", "getMyBookings", "getBookings", "getBooking", "patchBooking", "putBooking", "adminGetBookings"})
     */
    protected $advert;

    /**
     * @ORM\OneToMany(targetEntity="Course", mappedBy="booking", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden", "getBooking", "patchBooking", "getMyBookings", "putBooking", "adminGetBookings"})
     */
    protected $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set walletId
     *
     * @param string $walletId
     *
     * @return Booking
     */
    public function setWalletId($walletId)
    {
        $this->walletId = $walletId;

        return $this;
    }

    /**
     * Get walletId
     *
     * @return string
     */
    public function getWalletId()
    {
        return $this->walletId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Booking
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Booking
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Booking
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set advert
     *
     * @param Advert $advert
     *
     * @return Booking
     */
    public function setAdvert(Advert $advert = null)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Booking
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set transaction
     *
     * @param string $transaction
     *
     * @return Booking
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Booking
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Booking
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Booking
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Gets the value of price.
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets the value of price.
     *
     * @param mixed $price the price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Add course
     *
     * @param Course $course
     *
     * @return Booking
     */
    public function addCourse(Course $course)
    {
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Remove course
     *
     * @param Course $course
     */
    public function removeCourse(Course $course)
    {
        $this->courses->removeElement($course);
    }

    /**
     * Get courses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Set mangoPayTransactionId
     *
     * @param string $mangoPayTransactionId
     *
     * @return Booking
     */
    public function setMangoPayTransactionId($mangoPayTransactionId)
    {
        $this->mangoPayTransactionId = $mangoPayTransactionId;

        return $this;
    }

    /**
     * Get mangoPayTransactionId
     *
     * @return string
     */
    public function getMangoPayTransactionId()
    {
        return $this->mangoPayTransactionId;
    }

    /**
     * Set cancellation
     *
     * @param integer $cancellation
     *
     * @return Booking
     */
    public function setCancellation($cancellation)
    {
        $this->cancellation = $cancellation;

        return $this;
    }

    /**
     * Get cancellation
     *
     * @return integer
     */
    public function getCancellation()
    {
        return $this->cancellation;
    }
}
