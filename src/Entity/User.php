<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user_user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("none")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, JWTUserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_ADMIN';

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Groups({"getUsers", "getUser", "login_check", "register"})
     */
    private $id;

    /**
     * @ORM\Column(name="username", type="string", length=255)
     * @JMS\Groups({"getUsers", "getUser", "login_check", "register"})
     */
    private $username;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     * @JMS\Groups({"getUsers", "getUser", "login_check", "register"})
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    private $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="mangopayId", type="string", length=255, nullable=true)
     */
    private $mangopayId;

    /**
     * @var string
     *
     * @ORM\Column(name="planningId", type="string", length=255, nullable=true)
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    private $planningId;

    /**
     * @var string
     *
     * @ORM\Column(name="planningToken", type="string", length=255, nullable=true)
     * @JMS\Groups({"Default", "adminGetUsers", "adminGetUsers"})
     */
    private $planningToken;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $newsletter;

    /**
     * @var string
     *
     * @ORM\Column(name="adminComment", type="text", nullable = true)
     */
    private $adminComment;

    /**
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    private $roles;

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
    private $accessToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @JMS\Groups({"Default", "adminGetUsers"})
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $emailCanonical;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string|null
     * @ORM\Column(name="confirmation_token", type="string", length=255)
     */
    private $confirmationToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="password_requested_at", type="string", length=255, nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @var ArrayCollection
     */
    private $groups;

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
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user", cascade={"remove"}, fetch="EXTRA_LAZY")
     * @OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden"})
     */
    private $comments;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="coachs")
     * @ORM\JoinColumn(name="coach_manager_id", referencedColumnName="user_id", nullable=true)
     */
    private $coachManager;

    /**
     * @ORM\OneToMany(
     *      targetEntity="User",
     *      mappedBy="coachManager",
     *      indexBy="id",
     *      cascade={"remove", "persist"},
     *      fetch="EXTRA_LAZY"
     * )
     * @OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden", "getMe", "patchMe"})
     */
    private $coachs;

    /**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="user", fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden"})
     */
    private $bookings;

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
    private $vouchers;

    /**
     * @var Structure
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="users")
     * @ORM\JoinColumn(name="structure_id", referencedColumnName="structure_id", nullable=true)
     */
    private $structure;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *    targetEntity="StructureLink",
     *    mappedBy="user",
     *    cascade={"persist", "merge"},
     *    fetch="EXTRA_LAZY"
     * )
     * @JMS\Groups({"hidden"})
     */
    private $structureLinks;

    /**
     * @var AppMetadata
     *
     * @ORM\OneToOne(targetEntity="AppMetadata", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     * @JMS\Groups({"Default", "getUsers", "getUser"})
     */
    private $appMetadata;

    /**
     * @var UserMetadata
     * @ORM\OneToOne(targetEntity="UserMetadata", mappedBy="user", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"hidden", "getMe", "patchMe", "getPlanningUserInformations", "putBooking", "getBooking",
     *     "getBookingUser", "getBookingUser", "getUserById", "postEmailReminder", "adminGetUser", "getUsers",
     *     "getUser"})
     */
    private $userMetadata;

    /**
     * @ORM\ManyToMany(targetEntity="City", inversedBy="users", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="cities_teached",
     *      joinColumns={
     *          @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="city_id", referencedColumnName="city_id")
     *      }
     * )
     * @JMS\Groups({"hidden", "getUser", "postSimpleSearch", "countPostSimpleSearch", "getBestUsers"})
     */
    private $cities;

    /**
     * @ORM\OneToOne(targetEntity="Diploma", mappedBy="user", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"hidden", "getUser", "postSimpleSearch"})
     */
    private $diploma;

    /**
     * @ORM\OneToOne(targetEntity="Media", mappedBy="user", cascade={"remove", "persist"}, fetch="LAZY")
     * @JMS\Groups({"hidden", "getUser", "getMyUsers", "getUsers", "postSimpleSearch", "getBestUsers", "patchBooking",
     *     "getBooking", "putBooking", "getBookingUser", "getUserComplete"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="MeetingPoint", mappedBy="user", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @JMS\Type("ArrayCollection<App\Entity\MeetingPoint>")
     * @JMS\Groups({"hidden", "getUser", "getMyUsers"})
     */
    private $meetings;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SportTeached", mappedBy="user", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Groups({"hidden", "getUser", "getFormUser", "getMyUsers", "postSimpleSearch",
     *     "countPostSimpleSearch", "getSportsTeached", "getUserUsersports"})
     */
    private $sportsTeached;

    /**
     * User constructor.
     *
     * @param string|null $username
     * @param array|null  $roles
     */
    public function __construct(?string $username = null, ?array $roles = null)
    {
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->vouchers = new ArrayCollection();
        $this->sportsTeached = new ArrayCollection();
        $this->structureLinks = new ArrayCollection();
        $this->meetings = new ArrayCollection();
        $this->cities = new ArrayCollection();
        $this->enabled = false;
        $this->roles = array();
        $this->coachs = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromPayload($username, array $payload)
    {
        if (isset($payload['roles'])) {
            return new static($username, (array) $payload['roles']);
        }

        return new static($username);
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
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
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): ?string
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
    public function addGroup($group)
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
     * @param VouchersUsers $voucher
     */
    public function removeVoucher(VouchersUsers $voucher)
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
     * @return mixed
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * @param mixed $locked
     *
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
     *
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
     *
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
     *
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
     *
     * @return User
     */
    public function setCredentialsExpireAt(?\DateTime $credentialsExpireAt): User
    {
        $this->credentialsExpireAt = $credentialsExpireAt;
        return $this;
    }

    /**
     * @return UserMetadata
     */
    public function getUserMetadata(): ?UserMetadata
    {
        return $this->userMetadata;
    }

    /**
     * @param UserMetadata $userMetadata
     *
     * @return User
     */
    public function setUserMetadata(UserMetadata $userMetadata): User
    {
        $this->userMetadata = $userMetadata;
        return $this;
    }

    /**
     * @return Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param Structure $structure
     *
     * @return User
     */
    public function setStructure(Structure $structure = null): User
    {
        $this->structure = $structure;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStructureLinks(): ArrayCollection
    {
        return $this->structureLinks;
    }

    /**
     * @param ArrayCollection $structureLinks
     *
     * @return User
     */
    public function setStructureLinks(ArrayCollection $structureLinks): User
    {
        $this->structureLinks = $structureLinks;
        return $this;
    }

    /**
     * @return AppMetadata
     */
    public function getAppMetadata(): ?AppMetadata
    {
        return $this->appMetadata;
    }

    /**
     * @param AppMetadata $appMetadata
     *
     * @return User
     */
    public function setAppMetadata(AppMetadata $appMetadata): User
    {
        $appMetadata->setUser($this);
        $this->appMetadata = $appMetadata;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function getExpired(): ?bool
    {
        return $this->expired;
    }

    public function getCredentialsExpired(): ?bool
    {
        return $this->credentialsExpired;
    }

    public function getCoachManager(): ?self
    {
        return $this->coachManager;
    }

    public function setCoachManager(?self $coachManager): self
    {
        $this->coachManager = $coachManager;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getCoachs(): Collection
    {
        return $this->coachs;
    }

    public function addCoach(User $coach): self
    {
        if (!$this->coachs->contains($coach)) {
            $this->coachs[] = $coach;
            $coach->setCoachManager($this);
        }

        return $this;
    }

    public function removeCoach(User $coach): self
    {
        if ($this->coachs->contains($coach)) {
            $this->coachs->removeElement($coach);
            // set the owning side to null (unless already changed)
            if ($coach->getCoachManager() === $this) {
                $coach->setCoachManager(null);
            }
        }

        return $this;
    }

    public function addStructureLink(StructureLink $structureLink): self
    {
        if (!$this->structureLinks->contains($structureLink)) {
            $this->structureLinks[] = $structureLink;
            $structureLink->setUser($this);
        }

        return $this;
    }

    public function removeStructureLink(StructureLink $structureLink): self
    {
        if ($this->structureLinks->contains($structureLink)) {
            $this->structureLinks->removeElement($structureLink);
            // set the owning side to null (unless already changed)
            if ($structureLink->getUser() === $this) {
                $structureLink->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
        }

        return $this;
    }

    public function getDiploma(): ?Diploma
    {
        return $this->diploma;
    }

    public function setDiploma(?Diploma $diploma): self
    {
        $this->diploma = $diploma;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $diploma ? null : $this;
        if ($diploma->getUser() !== $newUser) {
            $diploma->setUser($newUser);
        }

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): self
    {
        $this->image = $image;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $image ? null : $this;
        if ($image->getUser() !== $newUser) {
            $image->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|MeetingPoint[]
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(MeetingPoint $meeting): self
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings[] = $meeting;
            $meeting->setUser($this);
        }

        return $this;
    }

    public function removeMeeting(MeetingPoint $meeting): self
    {
        if ($this->meetings->contains($meeting)) {
            $this->meetings->removeElement($meeting);
            // set the owning side to null (unless already changed)
            if ($meeting->getUser() === $this) {
                $meeting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SportTeached[]
     */
    public function getSportsTeached(): Collection
    {
        return $this->sportsTeached;
    }

    public function addSportsTeached(SportTeached $sportsTeached): self
    {
        if (!$this->sportsTeached->contains($sportsTeached)) {
            $this->sportsTeached[] = $sportsTeached;
            $sportsTeached->setUser($this);
        }

        return $this;
    }

    public function removeSportsTeached(SportTeached $sportsTeached): self
    {
        if ($this->sportsTeached->contains($sportsTeached)) {
            $this->sportsTeached->removeElement($sportsTeached);
            // set the owning side to null (unless already changed)
            if ($sportsTeached->getUser() === $this) {
                $sportsTeached->setUser(null);
            }
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $media ? null : $this;
        if ($media->getUser() !== $newUser) {
            $media->setUser($newUser);
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}