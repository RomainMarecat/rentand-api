<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user_user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("none")
 * @ORM\Entity(repositoryClass="Repository\UserRepository")
 */
class User implements UserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_ADMIN';
    private $password;
    private $salt;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    protected $id;

    /**
     * @JMS\Groups({"Default"})
     */
    protected $username;

    /**
     * @JMS\Groups({"Default", "adminGetUsers", "adminGetAdverts"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     * @JMS\Groups({"Default", "adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     * @JMS\Groups({"Default", "adminGetComments", "adminGetBookings", "adminGetUsers", "adminGetUser"})
     */
    protected $lastName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="gender", type="boolean", nullable=true)
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     * @Assert\Type(
     *     type="datetime",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=2, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 2,
     *      exactMessage = "The value must be a coutry code 3166-1_alpha-2",
     * )
     */
    protected $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="mangopayId", type="string", length=255, nullable=true)
     */
    protected $mangopayId;

    /**
     * @var string
     *
     * @ORM\Column(name="planningId", type="string", length=255, nullable=true)
     * @JMS\Groups({"Default", "adminGetAdverts"})
     */
    protected $planningId;

    /**
     * @var string
     *
     * @ORM\Column(name="planningToken", type="string", length=255, nullable=true)
     * @JMS\Groups({"Default", "adminGetAdverts", "adminGetUsers"})
     */
    protected $planningToken;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    protected $newsletter;

    /**
     * @var string
     *
     * @ORM\Column(name="adminComment", type="text", nullable = true)
     */
    protected $adminComment;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50, nullable=false, options={"default" : "zeemono"})
     */
    protected $type;

    /**
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    protected $roles;

    /**
     * @var bool
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var bool
     * @ORM\Column(name="expired", type="boolean")
     */
    private $expired;

    /**
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     */
    protected $accessToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    protected $expiresAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    protected $updatedAt;

    /**
     * @ORM\OneToOne(
     * targetEntity="Address",
     * mappedBy="user",
     * cascade={"remove", "persist"},
     * fetch="LAZY")
     * @JMS\Groups({"hidden", "getMe", "patchMe", "getUser", "getIsValidUser", "getUserByToken", "adminGetUsers", "adminGetUser"})
     */
    protected $address;

    /**
     * @ORM\OneToOne(targetEntity="Phone", mappedBy="user", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"hidden", "getMe", "patchMe", "getUser", "getPlanningUserInformations", "putBooking", "getBooking", "getBookingAdvert", "getBookingAdvert", "getAdvertById", "postEmailReminder", "adminGetUser"})
     */
    protected $phone;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user", cascade={"remove"}, fetch="EXTRA_LAZY")
     * @OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden"})
     */
    protected $comments;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Advert",
     *      mappedBy="user",
     *      indexBy="id",
     *      cascade={"remove", "persist"},
     *      fetch="EXTRA_LAZY"
     * )
     * @OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden", "getMe", "patchMe"})
     */
    protected $adverts;

    /**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="user", fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden"})
     */
    protected $bookings;

    /**
     * @var ArrayCollection Voucher $vouchers
     *
     * @ORM\OneToMany(
     *    targetEntity="App\Entity\VouchersUsers",
     *    mappedBy="user",
     *    cascade={"persist", "merge"},
     *    fetch="EXTRA_LAZY"
     * )
     * @JMS\Groups({"hidden"})
     * @JMS\MaxDepth(1)
     */
    protected $vouchers;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string|null
     * @ORM\Column(name="confirmation_token", type="string", length=255)
     */
    protected $confirmationToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="password_requested_at", type="string", length=255)
     */
    protected $passwordRequestedAt;

    /**
     * @var ArrayCollection
     */
    protected $groups;

    /**
     * @var bool
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked;

    /**
     * @var bool
     * @ORM\Column(name="credentials_expired", type="boolean")
     */
    private $credentialsExpired;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="credentials_expire_at", type="datetime", nullable=true)
     */
    private $credentialsExpireAt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->adverts = new ArrayCollection();
        $this->vouchers = new ArrayCollection();
        $this->enabled = false;
        $this->roles = array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if (13 === count($data)) {
            // Unserializing a User object from 1.3.x
            unset($data[4], $data[5], $data[6], $data[9], $data[10]);
            $data = array_values($data);
        } elseif (11 === count($data)) {
            // Unserializing a User from a dev version somewhere between 2.0-alpha3 and 2.0-beta1
            unset($data[4], $data[7], $data[8]);
            $data = array_values($data);
        }

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
            ) = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_values(array_unique($roles));
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup($group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = ucfirst($firstName);

        return $this;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = strtoupper($lastName);

        return $this;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     *
     * @return User
     */
    public function setNationality($nationality)
    {
        $this->nationality = strtoupper($nationality);

        return $this;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setCompulsoryStuff()
    {

        if ($this->getLastLogin() == null) {
            $this->setLastLogin(new \DateTime('now'));
        }
        if ($this->getNewsletter() == null) {
            $this->setNewsletter(false);
        }
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
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set gender
     *
     * @param boolean $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return boolean
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     *
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set mangopayId
     *
     * @param string $mangopayId
     *
     * @return User
     */
    public function setMangopayId($mangopayId)
    {
        $this->mangopayId = $mangopayId;

        return $this;
    }

    /**
     * Get mangopayId
     *
     * @return string
     */
    public function getMangopayId()
    {
        return $this->mangopayId;
    }

    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     *
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return boolean
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set adminComment
     *
     * @param string $adminComment
     *
     * @return User
     */
    public function setAdminComment($adminComment)
    {
        $this->adminComment = $adminComment;

        return $this;
    }

    /**
     * Get adminComment
     *
     * @return string
     */
    public function getAdminComment()
    {
        return $this->adminComment;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
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
     * @return User
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
     * Set address
     *
     * @param Address $address
     *
     * @return User
     */
    public function setAddress(Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param Phone $phone
     *
     * @return User
     */
    public function setPhone(Phone $phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return \Entity\Phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return User
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
     * Add advert
     *
     * @param Advert $advert
     *
     * @return User
     */
    public function addAdvert(Advert $advert)
    {
        $this->adverts[] = $advert;

        return $this;
    }

    /**
     * Remove advert
     *
     * @param Advert $advert
     */
    public function removeAdvert(Advert $advert)
    {
        $this->adverts->removeElement($advert);
    }

    /**
     * Get adverts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdverts()
    {
        return $this->adverts;
    }

    /**
     * Add booking
     *
     * @param Booking $booking
     *
     * @return User
     */
    public function addBooking(Booking $booking)
    {
        $this->bookings[] = $booking;

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
     * Add voucher
     *
     * @param \App\Entity\VouchersUsers $voucher
     *
     * @return User
     */
    public function addVoucher(\App\Entity\VouchersUsers $voucher)
    {
        $this->vouchers[] = $voucher;

        return $this;
    }

    /**
     * Remove voucher
     *
     * @param \App\Entity\VouchersUsers $voucher
     */
    public function removeVoucher(\App\Entity\VouchersUsers $voucher)
    {
        $this->vouchers->removeElement($voucher);
    }

    /**
     * Get vouchers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVouchers()
    {
        return $this->vouchers;
    }

    /**
     * Set planningId
     *
     * @param string $planningId
     *
     * @return User
     */
    public function setPlanningId($planningId)
    {
        $this->planningId = $planningId;

        return $this;
    }

    /**
     * Get planningId
     *
     * @return string
     */
    public function getPlanningId()
    {
        return $this->planningId;
    }

    /**
     * Set planningToken
     *
     * @param string $planningToken
     *
     * @return User
     */
    public function setPlanningToken($planningToken)
    {
        $this->planningToken = $planningToken;

        return $this;
    }

    /**
     * Get planningToken
     *
     * @return string
     */
    public function getPlanningToken()
    {
        return $this->planningToken;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken
     *
     * @return User
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * @param mixed $locked
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime|null $expiresAt
     * @return User
     */
    public function setExpiresAt(?\DateTime $expiresAt): User
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired;
    }

    /**
     * @param bool $expired
     * @return User
     */
    public function setExpired(bool $expired): User
    {
        $this->expired = $expired;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCredentialsExpired(): bool
    {
        return $this->credentialsExpired;
    }

    /**
     * @param bool $credentialsExpired
     * @return User
     */
    public function setCredentialsExpired(bool $credentialsExpired): User
    {
        $this->credentialsExpired = $credentialsExpired;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCredentialsExpireAt(): ?\DateTime
    {
        return $this->credentialsExpireAt;
    }

    /**
     * @param \DateTime|null $credentialsExpireAt
     * @return User
     */
    public function setCredentialsExpireAt(?\DateTime $credentialsExpireAt): User
    {
        $this->credentialsExpireAt = $credentialsExpireAt;
        return $this;
    }
}
