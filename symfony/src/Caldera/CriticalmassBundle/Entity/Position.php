<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="position")
 */
class Position
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="positions")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user_id;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $latitude;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $longitude;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $accuracy;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $altitude;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $altitudeAccuracy;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $heading;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $speed;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $timestamp;

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
     * @param \Caldera\CriticalmassBundle\Entity\User $userId
     * @return Position
     */
    public function setUserId(\Caldera\CriticalmassBundle\Entity\User $userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return \Caldera\CriticalmassBundle\Entity\User 
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
