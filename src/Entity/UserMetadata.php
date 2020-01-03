<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @JMS\Groups({"getUsers", "getUser", "getAccount", "patchUsers"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers"})
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers"})
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
     *     "getUser", "getAccount", "patchUsers"})
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
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount", "patchUsers"})
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
     * @var array
     * @JMS\Groups({"adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser", "getUsers",
     *     "getUser", "getAccount"})
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $languages;

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
     * @JMS\Groups({"hidden", "getMe", "patchMe", "getUser", "getIsValidUser", "getUserByToken", "adminGetUsers",
     *     "adminGetUser", "patchUsers"})
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity="Phone", mappedBy="userMetadata", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"hidden", "getMe", "patchMe", "getUser", "getPlanningUserInformations", "putBooking", "getBooking",
     *     "getBookingUser", "getBookingUser", "getUserById", "postEmailReminder", "adminGetUser"})
     */
    private $phone;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="userMetadata", cascade={"remove", "persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE"))
     * @JMS\Groups({"hidden", "getMe", "patchMe", "getUser", "getPlanningUserInformations", "putBooking", "getBooking",
     *     "getBookingUser", "getBookingUser", "getUserById", "postEmailReminder", "adminGetUser"})
     */
    private $user;

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
    
    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function setLanguages(?string $languages): self
    {
        $this->languages = $languages;

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
     * @param mixed $address
     *
     * @return UserMetadata
     */
    public function setAddress($address)
    {
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
     * @param mixed $phone
     *
     * @return UserMetadata
     */
    public function setPhone($phone)
    {
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
}
