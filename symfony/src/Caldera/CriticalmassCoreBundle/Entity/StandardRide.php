<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="standardride")
 */
class StandardRide
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="standardrides")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $time;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weekday;

    /**
     * @ORM\Column(type="integer")
     */
    protected $week;

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
     * Set time
     *
     * @param \DateTime $time
     * @return StandardRide
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return StandardRide
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return StandardRide
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return StandardRide
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set weekday
     *
     * @param integer $weekday
     * @return StandardRide
     */
    public function setWeekday($weekday)
    {
        $this->weekday = $weekday;
    
        return $this;
    }

    /**
     * Get weekday
     *
     * @return integer 
     */
    public function getWeekday()
    {
        return $this->weekday;
    }

    /**
     * Set week
     *
     * @param integer $week
     * @return StandardRide
     */
    public function setWeek($week)
    {
        $this->week = $week;
    
        return $this;
    }

    /**
     * Get week
     *
     * @return integer 
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * Set city
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\City $city
     * @return StandardRide
     */
    public function setCity(\Caldera\CriticalmassCoreBundle\Entity\City $city = null)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }
}