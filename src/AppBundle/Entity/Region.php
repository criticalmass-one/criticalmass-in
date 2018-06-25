<?php

namespace AppBundle\Entity;

use AppBundle\EntityInterface\AuditableInterface;
use AppBundle\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="region")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RegionRepository")
 */
class Region implements RouteableInterface, AuditableInterface
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
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="children", cascade={"persist"})
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Region
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Region
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Region
    {
        $this->slug = $slug;

        return $this;
    }

    public function getParent(): ?Region
    {
        return $this->parent;
    }

    public function setParent(Region $parent = null): Region
    {
        $this->parent = $parent;

        return $this;
    }

    public function isWorld(): bool
    {
        return $this->id == 1;
    }

    public function isLevel(int $levelNumber): bool
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

    public function __toString(): string
    {
        return $this->name;
    }

}
