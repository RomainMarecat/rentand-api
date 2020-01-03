<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Phone
 *
 * @ORM\Table(name="phone_phone")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("none")
 */
class Phone
{
    /**
     * @var integer
     *
     * @ORM\Column(name="phone_id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=45)
     * @JMS\Accessor(getter="getNumber", setter="setNumber")
     */
    private $number;

    /**
     * @var string $countryCode length 2
     *
     * @ORM\Column(name="countryCode", type="string", length=2)
     */
    private $countryCode;

    /**
     * @var string $countryNumber length 10
     *
     * @ORM\Column(name="country_number", type="string", length=10)
     */
    private $countryNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="checked", type="boolean")
     */
    private $checked;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=10)
     * @JMS\Exclude
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Exclude
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @JMS\Exclude
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="UserMetadata", inversedBy="phone")
     * @ORM\JoinColumn(name="user_metadata_id", referencedColumnName="user_metadata_id", onDelete="CASCADE"))
     * @JMS\Exclude
     * @JMS\Groups({"hidden"})
     */
    private $userMetadata;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
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
     * Set number
     *
     * @param string $number
     *
     * @return Phone
     */
    public function setNumber($number)
    {
        $this->number = $number;
        $this->checked = 0;

        $characts = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';
        for ($i = 0; $i < 6; $i++) {
            $token .= $characts[rand() % strlen($characts)];
        }
        $this->token = $token;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     *
     * @return Phone
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get checked
     *
     * @return boolean
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Phone
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Phone
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
     * @return Phone
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
     * @return Phone
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return Phone
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set countryNumber
     *
     * @param string $countryNumber
     *
     * @return Phone
     */
    public function setCountryNumber($countryNumber)
    {
        $this->countryNumber = $countryNumber;

        return $this;
    }

    /**
     * Get countryNumber
     *
     * @return string
     */
    public function getCountryNumber()
    {
        return $this->countryNumber;
    }

    public function getUserMetadata(): ?UserMetadata
    {
        return $this->userMetadata;
    }

    public function setUserMetadata(?UserMetadata $userMetadata): self
    {
        $this->userMetadata = $userMetadata;

        return $this;
    }
}
