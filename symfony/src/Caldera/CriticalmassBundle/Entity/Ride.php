<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ride")
 */
class Ride
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
  protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="City", inversedBy="rides")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
	protected $city_id;

	/**
	 * @ORM\Column(type="date")
	 */
	protected $date;

	/**
	 * @ORM\Column(type="time")
	 */
	protected $time;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $location;

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
     * Set date
     *
     * @param \DateTime $date
     * @return Ride
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Ride
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
     * @return Ride
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
     * Set city_id
     *
     * @param \Caldera\CriticalmassBundle\Entity\City $cityId
     * @return Ride
     */
    public function setCityId(\Caldera\CriticalmassBundle\Entity\City $cityId = null)
    {
        $this->city_id = $cityId;
    
        return $this;
    }

    /**
     * Get city_id
     *
     * @return \Caldera\CriticalmassBundle\Entity\City 
     */
    public function getCityId()
    {
        return $this->city_id;
    }
}