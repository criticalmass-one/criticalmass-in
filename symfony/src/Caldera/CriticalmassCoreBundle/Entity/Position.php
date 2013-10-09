<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * In dieser Entitaet wird ein Positions-Datum abgelegt, das mit den Eigen-
 * schaften aus der Geolocation-Spezifikation ausgestattet ist.
 *
 * @ORM\Entity()
 * @ORM\Table(name="position")
 */
class Position
{
	/**
	 * ID der Entitaet.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Sender dieses Datums.
	 *
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="positions")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * Tour, zu der diese Entitaet abgespeichert wurde.
	 *
	 * @ORM\ManyToOne(targetEntity="Ride", inversedBy="positions")
	 * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
	 */
	protected $ride;

	/**
	 * Breitengrad der Position.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $latitude;

	/**
	 * Laengengrad der Position.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $longitude;

	/**
	 * Vom Smartphone berechnete Genauigkeit dieser Positionsangabe.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $accuracy;

	/**
	 * Hoehe der Positon.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $altitude;

	/**
	 * Vom Smartphone berechnete Genauigkeit der Hoehenangabe.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $altitudeAccuracy;

	/**
	 * Wert des eventuell eingebauten Kompasses.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $heading;

	/**
	 * Momentane Geschwindigkeit des Geraetes.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $speed;

	/**
	 * Zeitpunkt, zu dem die obigen Angaben vom Smartphone ermittelt wurden.
	 *
	 * @ORM\Column(type="integer")
	 */
	protected $timestamp;

	/**
	 * Zeitpunkt der Erstellung dieses Positionsdatums.
	 *
	 * @ORM\Column(type="datetime")
	 */
	protected $creationDateTime;

	/**
	 * Vergleicht ein Positionsdatum mit einem weiteren Positionsdatum. Zwei Posi-
	 * tionen sind per Definition identisch, wenn Laengen- und Breitengrad ueber-
	 * einstimmen.
	 *
	 * @param Position $position: Zu vergleichendes Positions-Datum
	 *
	 * @return Boolean: True, wenn Laengen- und Breitengrad identisch sind
	 */
	public function isEqual(\Caldera\CriticalmassCoreBundle\Entity\Position $position)
	{
		if (($position->getLatitude() == $this->getLatitude()) and 
				($position->getLongitude() == $this->getLongitude()))
		{
			return true;
		}

		return false;
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
     * Set latitude
     *
     * @param float $latitude
     * @return Position
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
     * @return Position
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
     * Set accuracy
     *
     * @param float $accuracy
     * @return Position
     */
    public function setAccuracy($accuracy)
    {
        $this->accuracy = $accuracy;

        return $this;
    }

    /**
     * Get accuracy
     *
     * @return float 
     */
    public function getAccuracy()
    {
        return $this->accuracy;
    }

    /**
     * Set altitude
     *
     * @param float $altitude
     * @return Position
     */
    public function setAltitude($altitude)
    {
        $this->altitude = $altitude;

        return $this;
    }

    /**
     * Get altitude
     *
     * @return float 
     */
    public function getAltitude()
    {
        return $this->altitude;
    }

    /**
     * Set altitudeAccuracy
     *
     * @param float $altitudeAccuracy
     * @return Position
     */
    public function setAltitudeAccuracy($altitudeAccuracy)
    {
        $this->altitudeAccuracy = $altitudeAccuracy;

        return $this;
    }

    /**
     * Get altitudeAccuracy
     *
     * @return float 
     */
    public function getAltitudeAccuracy()
    {
        return $this->altitudeAccuracy;
    }

    /**
     * Set heading
     *
     * @param float $heading
     * @return Position
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get heading
     *
     * @return float 
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set speed
     *
     * @param float $speed
     * @return Position
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return float 
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Position
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return integer 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set user_id
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\User $userId
     * @return Position
     */
    public function setUserId(\Caldera\CriticalmassCoreBundle\Entity\User $userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set user
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\User $user
     * @return Position
     */
    public function setUser(\Caldera\CriticalmassCoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Position
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime 
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * Set ride
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\Ride $ride
     * @return Position
     */
    public function setRide(\Caldera\CriticalmassCoreBundle\Entity\Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\Ride
     */
    public function getRide()
    {
        return $this->ride;
    }
}