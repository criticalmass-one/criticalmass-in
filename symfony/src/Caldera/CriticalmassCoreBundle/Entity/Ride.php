<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stellt eine einzelne Tour einer Critical Mass dar.
 *
 * @ORM\Entity
 * @ORM\Table(name="ride")
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassCoreBundle\Entity\RideRepository")
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
    protected $id;

	/**
	 * Numerische ID der dazugehörigen Stadt, in der die Tour stattfindet.
	 *
	 * @ORM\ManyToOne(targetEntity="City", inversedBy="rides")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
    protected $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * Startdatum und -uhrzeit der Tour.
     *
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

	/**
	 * Angabe, ob die Zeitangabe in den Tourinformationen dargestellt werden soll.
	 *
	 * @ORM\Column(type="boolean")
	 */
    protected $hasTime;

	/**
	 * Angabe, ob der Treffpunkt in den Tourinformationen dargestellt werden soll.
	 *
	 * @ORM\Column(type="boolean")
	 */
    protected $hasLocation;

	/**
	 * Bezeichnung des Treffpunktes der Tour als Zeichenkette.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
    protected $location;

	/**
	 * Breitengrad des Treffpunktes.
	 *
	 * @ORM\Column(type="float")
	 */
    protected $latitude;

	/**
	 * Laengengrad des Treffpunktes.
	 *
	 * @ORM\Column(type="float")
	 */
    protected $longitude;

	/**
	 * Schalter fuer den God-Mode dieser Tour. Wenn der Gode-Mode aktiviert ist,
	 * werden lediglich die Positionsdaten eines Administrators zur Berechnung
	 * herangezogen.
	 *
	 * @ORM\Column(type="integer")
	 */
    protected $godMode = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $optimizedGpxContent;

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
     * @param \DateTime $dateTime
     * @return Ride
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
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
     * @param \Caldera\CriticalmassCoreBundle\Entity\City $city
     * @return Ride
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

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
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

    public function setOptimizedGpxContent($optimizedGpxContent)
    {
        $this->optimizedGpxContent = $optimizedGpxContent;
    }

    public function getOptimizedGpxContent()
    {
        return $this->optimizedGpxContent;
    }

    public function isRideRolling()
    {
        $rideTimeStamp = $this->getDateTime()->format('U');
        $tmp = new \DateTime();
        $nowTimeStamp = $tmp->getTimeStamp();

        if ($rideTimeStamp + 900 < $nowTimeStamp)
        {
            return true;
        }

        return false;
    }

    public function getPublicLatitude()
    {
        if ($this->getHasLocation())
        {
            return $this->getLatitude();
        }
        else
        {
            return $this->getCity()->getLatitude();
        }
    }

    public function getPublicLongitude()
    {
        if ($this->getHasLocation())
        {
            return $this->getLongitude();
        }
        else
        {
            return $this->getCity()->getLongitude();
        }
    }

    public function isEqual(Ride $ride)
    {
        return $ride->getId() == $this->getId();
    }

    public function __toString()
    {
        if ($this->city)
        {
            return $this->city->getTitle()." - ".$this->getDateTime()->format("Y-m-d");
        }
        else
        {
            return $this->getDateTime()->format("Y-m-d");
        }
    }

    public function __construct()
    {
        $this->dateTime = new \DateTime();
        $this->latitude = 0.0;
        $this->longitude = 0.0;
    }
}
