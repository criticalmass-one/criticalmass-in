<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tilelayer")
 * @ORM\Entity()
 */
class TileLayer
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
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $attribution;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $plusOnly;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $public;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $standard;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAttribution()
    {
        return $this->attribution;
    }

    public function setAttribution($attribution)
    {
        $this->attribution = $attribution;
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * Get plusOnly
     *
     * @return boolean
     */
    public function getPlusOnly()
    {
        return $this->plusOnly;
    }

    /**
     * Set plusOnly
     *
     * @param boolean $plusOnly
     * @return TileLayer
     */
    public function setPlusOnly($plusOnly)
    {
        $this->plusOnly = $plusOnly;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return TileLayer
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return TileLayer
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get standard
     *
     * @return boolean
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Set standard
     *
     * @param boolean $standard
     * @return TileLayer
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }
}
