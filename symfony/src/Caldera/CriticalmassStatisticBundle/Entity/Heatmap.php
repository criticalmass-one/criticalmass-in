<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 01.06.14
 * Time: 02:24
 */

namespace Caldera\CriticalmassStatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Caldera\CriticalmassCoreBundle\Entity\Ride;

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
     * @ORM\ManyToMany(targetEntity="Caldera\CriticalmassCoreBundle\Entity\City")
     * @ORM\JoinTable(name="heatmap_city",
     *      joinColumns={@ORM\JoinColumn(name="heatmap_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="city_id", referencedColumnName="id")}
     *      )
     */
    protected $cities;

    /**
     * @ORM\ManyToMany(targetEntity="Caldera\CriticalmassCoreBundle\Entity\Ride")
     * @ORM\JoinTable(name="heatmap_ride",
     *      joinColumns={@ORM\JoinColumn(name="heatmap_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ride_id", referencedColumnName="id")}
     *      )
     */
    protected $rides;

    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rides = new \Doctrine\Common\Collections\ArrayCollection();
        $this->identifier = md5(microtime());
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

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function addRide(\Caldera\CriticalmassCoreBundle\Entity\Ride $ride)
    {
        $this->rides[] = $ride;

        return $this;
    }

    public function removeRide(\Caldera\CriticalmassCoreBundle\Entity\Ride $ride)
    {
        $this->rides->removeElement($ride);
    }

    public function getRides()
    {
        return $this->rides;
    }

    public function getPublic()
    {
        return $this->public;
    }

    public function setPublic($public)
    {
        $this->public = $public;
    }

    public function addCity(\Caldera\CriticalmassCoreBundle\Entity\City $city)
    {
        $this->cities[] = $city;

        return $this;
    }

    public function removeCity(\Caldera\CriticalmassCoreBundle\Entity\City $city)
    {
        $this->cities->removeElement($city);
    }

    public function getCities()
    {
        return $this->cities;
    }
}