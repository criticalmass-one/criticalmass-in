<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stellt eine einzelne Tour einer Critical Mass dar.
 *
 * @ORM\Table(name="ride")
 */
class Ride
{
	/**
	 * Numerische ID der Tour.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Numerische ID der dazugehörigen Stadt, in der die Tour stattfindet.
	 *
	 * @ORM\ManyToOne(targetEntity="City", inversedBy="rides")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
	private $city;

	/**
	 * Datum der Tour vom Typ DateTime.
	 *
	 * @ORM\Column(type="date")
	 */
	private $date;

	/**
	 * Angabe, ob die Zeitangabe in den Tourinformationen dargestellt werden soll.
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hasTime;

	/**
	 * Uhrzeit der Tour vom Typ DateTime.
	 *
	 * @ORM\Column(type="time")
	 */
	private $time;

	/**
	 * Angabe, ob der Treffpunkt in den Tourinformationen dargestellt werden soll.
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hasLocation;

	/**
	 * Bezeichnung des Treffpunktes der Tour als Zeichenkette.
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $location;

	/**
	 * Technische Bezeichnung des Treffpunktes der Tour als Zeichenkette fuer die
	 * Darstellung der Karte.
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $mapLocation;

	/**
	 * Breitengrad des Treffpunktes.
	 *
	 * @ORM\Column(type="float")
	 */
	private $latitude;

	/**
	 * Laengengrad des Treffpunktes.
	 *
	 * @ORM\Column(type="float")
	 */
	private $longitude;

	/**
	 * Schalter fuer den God-Mode dieser Tour. Wenn der Gode-Mode aktiviert ist,
	 * werden lediglich die Positionsdaten eines Administrators zur Berechnung
	 * herangezogen.
	 *
	 * @ORM\Column(type="integer")
	 */
	private $godMode = 0;

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
     * Set hasTime
     *
     * @param boolean $hasTime
     * @return Ride
     */
    public function setHasTime($hasTime)
    {
        $this->hasTime = $hasTime;
    
        return $this;
    }

    /**
     * Get hasTime
     *
     * @return boolean 
     */
    public function getHasTime()
    {
        return $this->hasTime;
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
     * Set hasLocation
     *
     * @param boolean $hasLocation
     * @return Ride
     */
    public function setHasLocation($hasLocation)
    {
        $this->hasLocation = $hasLocation;
    
        return $this;
    }

    /**
     * Get hasLocation
     *
     * @return boolean 
     */
    public function getHasLocation()
    {
        return $this->hasLocation;
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
     * Set city
     *
     * @param \Caldera\CriticalmassBundle\Entity\City $city
     * @return Ride
     */
    public function setCity(\Caldera\CriticalmassBundle\Entity\City $city = null)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\CriticalmassBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Ride
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
     * @return Ride
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
     * Set mapLocation
     *
     * @param string $mapLocation
     * @return Ride
     */
    public function setMapLocation($mapLocation)
    {
        $this->mapLocation = $mapLocation;
    
        return $this;
    }

    /**
     * Get mapLocation
     *
     * @return string 
     */
    public function getMapLocation()
    {
        return $this->mapLocation;
    }

    /**
     * Set godMode
     *
     * @param integer $godMode
     * @return Ride
     */
    public function setGodMode($godMode)
    {
        $this->godMode = $godMode;
    
        return $this;
    }

    /**
     * Get godMode
     *
     * @return integer 
     */
    public function getGodMode()
    {
        return $this->godMode;
    }
}
