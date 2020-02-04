<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserMetadataRepository")
 */
class UserMetadata
{
    /**
     * @var string
     *
     * @ORM\Column(name="user_metadata_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getUsers", "getUser", "getAccount", "patchUsers", "registerUser"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers", "registerUser"})
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers", "registerUser"})
     */
    private $lastname;

    /**
     * @var boolean
     *
     * @ORM\Column(name="gender", type="boolean", nullable=true)
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers"})
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers", "registerUser"})
     * @Assert\Type(
     *     type="datetime",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $birthday;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="nationality", referencedColumnName="country_id", nullable=true)
     * @JMS\Groups({"getUsers", "getUser", "getAccount", "patchUsers"})
     */
    private $nationality;

    /**
     * @var Language
     *
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers"})
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumn(name="mother_lang", referencedColumnName="language_id", nullable=true)
     */
    private $motherLang;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"firstname","lastname"})
     * @JMS\Groups({"getUsers", "getUser"})
     */
    private $slug;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="media_id", nullable=true)
     * @JMS\Groups({"getUsers", "getUser", "getAccount"})
     */
    private $media;

    /**
     * @ORM\OneToOne(
     * targetEntity="Address",
     * mappedBy="userMetadata",
     * cascade={"remove", "persist"},
     * fetch="LAZY")
     * @JMS\Groups({"", "getMe", "patchMe", "getUser", "getIsValidUser", "getUserByToken", "adminGetUsers",
     *     "adminGetUser", "patchUsers", "getAccount"})
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity="Phone", mappedBy="userMetadata", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"getUser", "putBooking", "patchUsers", "getAccount"})
     */
    private $phone;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="userMetadata", cascade={"remove", "persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE"))
     * @JMS\Groups({"", "getMe", "patchMe", "getUser", "getPlanningUserInformations", "putBooking", "getBooking",
     *     "getBookingUser", "getBookingUser", "getUserById", "postEmailReminder", "adminGetUser"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Language", inversedBy="userMetadata")
     * @ORM\JoinTable(name="user_languages",
     *      joinColumns={@ORM\JoinColumn(name="user_metadata_id", referencedColumnName="user_metadata_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="language_id")})
     * @JMS\Groups({"getUsers", "getUser", "getAccount"})
     */
    private $languages;

    public function __construct()
    {
        $this->languages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address|null $address
     *
     * @return UserMetadata
     */
    public function setAddress(Address $address = null)
    {
        $address->setUserMetadata($this);
        $this->address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param Phone|null $phone
     *
     * @return UserMetadata
     */
    public function setPhone(Phone $phone = null)
    {
        $phone->setUserMetadata($this);
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return UserMetadata
     */
    public function setUser(User $user): UserMetadata
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Media
     */
    public function getMedia(): Media
    {
        return $this->media;
    }

    /**
     * @param Media $media
     *
     * @return UserMetadata
     */
    public function setMedia(Media $media): UserMetadata
    {
        $this->media = $media;
        return $this;
    }

    public function getNationality(): ?Country
    {
        return $this->nationality;
    }

    public function setNationality(?Country $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getMotherLang(): ?Language
    {
        return $this->motherLang;
    }

    public function setMotherLang(?Language $motherLang): self
    {
        $this->motherLang = $motherLang;

        return $this;
    }

    public function getGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(?bool $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages[] = $language;
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        if ($this->languages->contains($language)) {
            $this->languages->removeElement($language);
        }

        return $this;
    }
}
