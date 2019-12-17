<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Advert
 *
 * @ORM\Table(name="advert_advert")
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("none")
 */
class Advert
{
    /**
     * @var integer
     *
     * @ORM\Column(name="advert_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"Default", "adminGetAdverts"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="integer")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable = true)
     * @JMS\Accessor(getter="getFirstName",setter="setFirstName")
     * @JMS\Groups({"Default", "adminGetComments", "adminGetAdverts", "adminGetBookings"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable = true)
     * @JMS\Accessor(getter="getLastName",setter="setLastName")
     * @JMS\Groups({"Default", "adminGetComments", "adminGetAdverts", "adminGetBookings"})
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"firstName"})
     */
    protected $slug;

    /**
     * @var array
     *
     * @ORM\Column(name="languages", type="array")
     */
    protected $languages;

    /**
     * @var array
     *
     * @ORM\Column(name="passions", type="array", nullable=true)
     */
    protected $passions;

    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @JMS\Groups({"Default", "adminGetAdverts"})
     */
    protected $statut;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     * @JMS\Groups({"Default", "adminGetAdverts"})
     */
    protected $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"Default", "adminGetAdverts"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @JMS\Groups({"Default", "adminGetAdverts"})
     */
    protected $updatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="flux1", type="boolean", nullable=true)
     */
    protected $flux1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="flux2", type="boolean", nullable=true)
     */
    protected $flux2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="flux3", type="boolean", nullable=true)
     */
    protected $flux3;

    /**
     * @var boolean
     *
     * @ORM\Column(name="flux4", type="boolean", nullable=true)
     */
    protected $flux4;

    /**
     * @var boolean
     *
     * @ORM\Column(name="flux5", type="boolean", nullable=true)
     */
    protected $flux5;

    /**
     * @var string
     *
     * @ORM\Column(name="cancel", type="integer")
     * @JMS\Groups({"Default", "adminGetAdverts"})
     */
    protected $cancel;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="adverts", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     * @JMS\Groups({"hidden", "getMyBookings", "getFormAdvert", "postSimpleSearch", "getBooking", "getBookingAdvert", "patchBooking", "putBooking", "getAdvertById", "postEmailReminder", "adminGetAdverts"})
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="AdvertTranslation", mappedBy="advert", indexBy="locale", cascade={"remove", "persist"}, fetch="LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Type("ArrayCollection<Entity\AdvertTranslation>")
     * @JMS\Groups({"hidden", "getAdvert", "getMyAdverts", "postSimpleSearch", "getAdvertTranslations", "getAdvertById"})
     */
    protected $translations;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="advert", cascade={"remove"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden", "getAdverts", "getAdvert", "postSimpleSearch"})
     */
    protected $comments;

    /**
     * @ORM\ManyToMany(targetEntity="City", inversedBy="adverts", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="advert_cities",
     *      joinColumns={
     *          @ORM\JoinColumn(name="advert_id", referencedColumnName="advert_id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="city_id", referencedColumnName="city_id")
     *      }
     * )
     * @JMS\Groups({"hidden", "getAdvert", "postSimpleSearch", "countPostSimpleSearch", "getBestAdverts"})
     */
    protected $cities;

    /**
     * @ORM\OneToOne(targetEntity="Diploma", mappedBy="advert", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"hidden", "getAdvert", "postSimpleSearch"})
     */
    protected $diploma;

    /**
     * @ORM\OneToOne(targetEntity="Image", mappedBy="advert", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"hidden", "getAdvert", "getMyAdverts", "getAdverts", "postSimpleSearch", "getBestAdverts", "patchBooking", "getBooking", "putBooking", "getBookingAdvert", "getAdvertComplete"})
     */
    protected $image;

    /**
     * @ORM\OneToMany(targetEntity="AdvertSport", mappedBy="advert", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden", "getAdvert", "getFormAdvert", "getMyAdverts", "postSimpleSearch", "countPostSimpleSearch", "getAdvertSports", "getAdvertAdvertSports"})
     * @JMS\Type("ArrayCollection<Entity\AdvertSport>")
     */
    protected $sports;

    /**
     * @ORM\OneToMany(targetEntity="Meeting", mappedBy="advert", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @JMS\Type("ArrayCollection<Entity\Meeting>")
     * @JMS\Groups({"hidden", "getAdvert", "getMyAdverts"})
     */
    protected $meetings;

    /**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="advert", cascade={"remove"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden", "getBookingAdvert"})
     */
    protected $bookings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meetings = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->sports = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Advert
     */
    public function setFirstName($firstName)
    {
        $this->firstName = ucfirst($firstName);

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Advert
     */
    public function setLastName($lastName)
    {
        $this->lastName = strtoupper($lastName);

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set languages
     *
     * @param array $languages
     *
     * @return Advert
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Advert
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Advert
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
     * @return Advert
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
     * @return Advert
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
     * Add translation
     *
     * @param AdvertTranslation $translation
     *
     * @return Advert
     */
    public function addTranslation(AdvertTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param AdvertTranslation $translation
     */
    public function removeTranslation(AdvertTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Advert
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set diploma
     *
     * @param Diploma $diploma
     *
     * @return Advert
     */
    public function setDiploma(Diploma $diploma = null)
    {
        $this->diploma = $diploma;

        return $this;
    }

    /**
     * Get diploma
     *
     * @return \Entity\Diploma
     */
    public function getDiploma()
    {
        return $this->diploma;
    }

    /**
     * Set image
     *
     * @param Image $image
     *
     * @return Advert
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get sports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * Set title
     *
     * @param integer $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return integer
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add meeting
     *
     * @param Meeting $meeting
     *
     * @return Advert
     */
    public function addMeeting(Meeting $meeting)
    {
        $this->meetings[] = $meeting;

        return $this;
    }

    /**
     * Remove meeting
     *
     * @param Meeting $meeting
     */
    public function removeMeeting(Meeting $meeting)
    {
        $this->meetings->removeElement($meeting);
    }

    /**
     * Get meetings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMeetings()
    {
        return $this->meetings;
    }


    /**
     * Add booking
     *
     * @param Booking $booking
     *
     * @return Advert
     */
    public function addBooking(Booking $booking)
    {
        $this->bookings[] = $booking;

        return $this;
    }

    /**
     * @param AdvertSport $sport
     *
     * @return Advert
     */
    public function addSport(AdvertSport $sport)
    {
        $this->sports[] = $sport;

        return $this;
    }

    /**
     * Remove booking
     *
     * @param Booking $booking
     */
    public function removeBooking(Booking $booking)
    {
        $this->bookings->removeElement($booking);

        return $this;
    }

    /**
     * Remove booking
     *
     * @param AdvertSport $sport
     */
    public function removeSport(AdvertSport $sport)
    {
        $this->sports->removeElement($sport);

        return $this;
    }

    /**
     * Get bookings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * Set flux1
     *
     * @param boolean $flux1
     *
     * @return Advert
     */
    public function setFlux1($flux1)
    {
        $this->flux1 = $flux1;

        return $this;
    }

    /**
     * Get flux1
     *
     * @return boolean
     */
    public function getFlux1()
    {
        return $this->flux1;
    }

    /**
     * Set flux2
     *
     * @param boolean $flux2
     *
     * @return Advert
     */
    public function setFlux2($flux2)
    {
        $this->flux2 = $flux2;

        return $this;
    }

    /**
     * Get flux2
     *
     * @return boolean
     */
    public function getFlux2()
    {
        return $this->flux2;
    }

    /**
     * Set flux3
     *
     * @param boolean $flux3
     *
     * @return Advert
     */
    public function setFlux3($flux3)
    {
        $this->flux3 = $flux3;

        return $this;
    }

    /**
     * Get flux3
     *
     * @return boolean
     */
    public function getFlux3()
    {
        return $this->flux3;
    }

    /**
     * Set flux4
     *
     * @param boolean $flux4
     *
     * @return Advert
     */
    public function setFlux4($flux4)
    {
        $this->flux4 = $flux4;

        return $this;
    }

    /**
     * Get flux4
     *
     * @return boolean
     */
    public function getFlux4()
    {
        return $this->flux4;
    }

    /**
     * Set flux5
     *
     * @param boolean $flux5
     *
     * @return Advert
     */
    public function setFlux5($flux5)
    {
        $this->flux5 = $flux5;

        return $this;
    }

    /**
     * Get flux5
     *
     * @return boolean
     */
    public function getFlux5()
    {
        return $this->flux5;
    }

    /**
     * Add city
     *
     * @param City $city
     *
     * @return Advert
     */
    public function addCity(City $city)
    {
        $this->cities[] = $city;

        return $this;
    }

    /**
     * Remove city
     *
     * @param City $city
     */
    public function removeCity(City $city)
    {
        $this->cities->removeElement($city);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Set passions
     *
     * @param array $passions
     *
     * @return Advert
     */
    public function setPassions($passions)
    {
        $this->passions = $passions;

        return $this;
    }

    /**
     * Get passions
     *
     * @return array
     */
    public function getPassions()
    {
        return $this->passions;
    }

    /**
     * Set cancel
     *
     * @param integer $cancel
     *
     * @return Advert
     */
    public function setCancel($cancel)
    {
        $this->cancel = $cancel;

        return $this;
    }

    /**
     * Get cancel
     *
     * @return integer
     */
    public function getCancel()
    {
        return $this->cancel;
    }

    /**
     * Gets the value of enabled.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the value of enabled.
     *
     * @param boolean $enabled the enabled
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
