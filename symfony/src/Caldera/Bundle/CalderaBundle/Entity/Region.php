<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="region")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\RegionRepository")
 */
class Region
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Region", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="region")
     */
    protected $cities;

    public function __toString()
    {
        return $this->name;
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
     * @return Region
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
     * Set description
     *
     * @param string $description
     * @return Region
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Region
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
     * Set parent
     *
     * @param Region $region
     * @return Region
     */
    public function setParent(Region $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Region
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function isWorld()
    {
        return $this->id == 1;
    }

    public function isLevel($levelNumber)
    {
        if ($levelNumber == 0 && $this->parent == null) {
            return $this->id == 1;
        } elseif ($levelNumber == 1 && $this->parent != null) {
            return $this->parent->id == 1;
        } elseif ($levelNumber == 2 && $this->parent != null && $this->parent->parent != null) {
            return $this->parent->parent->id == 1;
        } elseif ($levelNumber == 3 && $this->parent != null && $this->parent->parent != null && $this->parent->parent->parent != null) {
            return $this->parent->parent->parent->id == 1;
        }

        return false;
    }

}
