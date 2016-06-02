<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="heatmap")
 */
class Heatmap
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $identifier;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $public;

    /**
     * @ORM\ManyToMany(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\City")
     * @ORM\JoinTable(name="heatmap_city",
     *      joinColumns={@ORM\JoinColumn(name="heatmap_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="city_id", referencedColumnName="id")}
     *      )
     */
    protected $cities;

    /**
     * @ORM\ManyToMany(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\Ride")
     * @ORM\JoinTable(name="heatmap_ride",
     *      joinColumns={@ORM\JoinColumn(name="heatmap_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ride_id", referencedColumnName="id")}
     *      )
     */
    protected $rides;

    /**
     * @ORM\ManyToMany(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\Track")
     * @ORM\JoinTable(name="heatmap_track",
     *      joinColumns={@ORM\JoinColumn(name="heatmap_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="track_id", referencedColumnName="id")}
     *      )
     */
    protected $tracks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rides = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tracks = new \Doctrine\Common\Collections\ArrayCollection();

        $this->identifier = md5(microtime());
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
     * Set title
     *
     * @param string $title
     * @return Heatmap
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Heatmap
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
     * Set identifier
     *
     * @param string $identifier
     * @return Heatmap
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return Heatmap
     */
    public function setPublic($public)
    {
        $this->public = $public;

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
     * Add cities
     *
     * @param City $cities
     * @return Heatmap
     */
    public function addCity(City $cities)
    {
        $this->cities[] = $cities;

        return $this;
    }

    /**
     * Remove cities
     *
     * @param City $cities
     */
    public function removeCity(City $cities)
    {
        $this->cities->removeElement($cities);
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
     * Add rides
     *
     * @param Ride $rides
     * @return Heatmap
     */
    public function addRide(Ride $rides)
    {
        $this->rides[] = $rides;

        return $this;
    }

    /**
     * Remove rides
     *
     * @param Ride $rides
     */
    public function removeRide(Ride $rides)
    {
        $this->rides->removeElement($rides);
    }

    /**
     * Get rides
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRides()
    {
        return $this->rides;
    }

    /**
     * Add tracks
     *
     * @param Track $tracks
     * @return Heatmap
     */
    public function addTrack(Track $tracks)
    {
        $this->tracks[] = $tracks;

        return $this;
    }

    /**
     * Remove tracks
     *
     * @param Track $tracks
     */
    public function removeTrack(Track $tracks)
    {
        $this->tracks->removeElement($tracks);
    }

    /**
     * Get tracks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTracks()
    {
        return $this->tracks;
    }
}
