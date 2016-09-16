<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\FacebookRidePropertiesRepository")
 * @ORM\Table(name="facebook_ride_properties")
 */
class FacebookRideProperties
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="facebookProperties")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberAttending;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberMaybe;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberDeclined;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberInterested;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberNoreply;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $locationAddress;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set name
     *
     * @param string $name
     * @return FacebookRideProperties
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return FacebookRideProperties
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
     * Set numberAttending
     *
     * @param integer $numberAttending
     * @return FacebookRideProperties
     */
    public function setNumberAttending($numberAttending)
    {
        $this->numberAttending = $numberAttending;

        return $this;
    }

    /**
     * Get numberAttending
     *
     * @return integer 
     */
    public function getNumberAttending()
    {
        return $this->numberAttending;
    }

    /**
     * Set numberMaybe
     *
     * @param integer $numberMaybe
     * @return FacebookRideProperties
     */
    public function setNumberMaybe($numberMaybe)
    {
        $this->numberMaybe = $numberMaybe;

        return $this;
    }

    /**
     * Get numberMaybe
     *
     * @return integer 
     */
    public function getNumberMaybe()
    {
        return $this->numberMaybe;
    }

    /**
     * Set numberDeclined
     *
     * @param integer $numberDeclined
     * @return FacebookRideProperties
     */
    public function setNumberDeclined($numberDeclined)
    {
        $this->numberDeclined = $numberDeclined;

        return $this;
    }

    /**
     * Get numberDeclined
     *
     * @return integer 
     */
    public function getNumberDeclined()
    {
        return $this->numberDeclined;
    }

    /**
     * Set numberInterested
     *
     * @param integer $numberInterested
     * @return FacebookRideProperties
     */
    public function setNumberInterested($numberInterested)
    {
        $this->numberInterested = $numberInterested;

        return $this;
    }

    /**
     * Get numberInterested
     *
     * @return integer 
     */
    public function getNumberInterested()
    {
        return $this->numberInterested;
    }

    /**
     * Set numberNoreply
     *
     * @param integer $numberNoreply
     * @return FacebookRideProperties
     */
    public function setNumberNoreply($numberNoreply)
    {
        $this->numberNoreply = $numberNoreply;

        return $this;
    }

    /**
     * Get numberNoreply
     *
     * @return integer 
     */
    public function getNumberNoreply()
    {
        return $this->numberNoreply;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return FacebookRideProperties
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return FacebookRideProperties
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set updatedTime
     *
     * @param \DateTime $updatedTime
     * @return FacebookRideProperties
     */
    public function setUpdatedTime($updatedTime)
    {
        $this->updatedTime = $updatedTime;

        return $this;
    }

    /**
     * Get updatedTime
     *
     * @return \DateTime 
     */
    public function getUpdatedTime()
    {
        return $this->updatedTime;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return FacebookRideProperties
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set ride
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Ride $ride
     * @return FacebookRideProperties
     */
    public function setRide(\Caldera\Bundle\CalderaBundle\Entity\Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\Ride 
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return FacebookRideProperties
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
     * Set locationAddress
     *
     * @param string $locationAddress
     * @return FacebookRideProperties
     */
    public function setLocationAddress($locationAddress)
    {
        $this->locationAddress = $locationAddress;

        return $this;
    }

    /**
     * Get locationAddress
     *
     * @return string 
     */
    public function getLocationAddress()
    {
        return $this->locationAddress;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return FacebookRideProperties
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
     * @return FacebookRideProperties
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
}
