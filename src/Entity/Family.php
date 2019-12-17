<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Family
 *
 * @ORM\Table(name="family_family")
 * @ORM\Entity(repositoryClass="Repository\FamilyRepository")
 * @JMS\ExclusionPolicy("none")
 */
class Family
{
    /**
     * @var int
     *
     * @ORM\Column(name="family_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, separator="-", updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    protected $level;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="FamilyTranslation", mappedBy="family", indexBy="locale", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Type("ArrayCollection<Entity\FamilyTranslation>")
     * @JMS\Groups({"hidden", "getFormFamily", "getFamily", "getParentFamilies", "getFamiliesByParent"})
     */
    protected $translations;

    /**
     * @ORM\ManyToOne(targetEntity="Family", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="family_id", onDelete="CASCADE")
     * @JMS\Groups({"hidden", "getFormFamily", "getFamily"})
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Family", mappedBy="parent", cascade={"remove", "persist"}, fetch="EXTRA_LAZY")
     * @JMS\Groups({"hidden", "getFamiliesByParent"})
     */
    protected $children;

    /**
     * @JMS\Type("ArrayCollection<App\Entity\Sport>")
     * @ORM\ManyToMany(targetEntity="App\Entity\Sport", inversedBy="families", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      name="families_sports",
     *      joinColumns={
     *          @ORM\JoinColumn(name="family_id", referencedColumnName="family_id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     *      }
     * )
     * @JMS\Groups({"hidden", "getFormFamily", "getFamily"})
     */
    protected $sports;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Passion
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Passion
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Passion
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
     * @return Passion
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
     * Add translation
     *
     * @param FamilyTranslation $translation
     *
     * @return Passion
     */
    public function addTranslation(FamilyTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param FamilyTranslation $translation
     */
    public function removeTranslation(FamilyTranslation $translation)
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
     * Set level
     *
     * @param integer $level
     *
     * @return Family
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set parent
     *
     * @param Family $parent
     *
     * @return Family
     */
    public function setParent(Family $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Entity\Family
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param Family $child
     *
     * @return Family
     */
    public function addChild(Family $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param Family $child
     */
    public function removeChild(Family $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add sport
     *
     * @param Sport $sport
     *
     * @return Family
     */
    public function addSport(Sport $sport)
    {
        $this->sports[] = $sport;

        return $this;
    }

    /**
     * Remove sport
     *
     * @param Sport $sport
     */
    public function removeSport(Sport $sport)
    {
        $this->sports->removeElement($sport);
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
}
