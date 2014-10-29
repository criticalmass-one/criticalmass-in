<?php

namespace Caldera\CriticalmassStatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ride_estimate")
 */
class RideEstimate
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
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="estimates")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * Tour, zu der diese Entitaet abgespeichert wurde.
	 *
	 * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\Ride", inversedBy="estimates")
	 * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
	 */
	protected $ride;

    /**
     * @ORM\OneToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\Track", mappedBy="rideEstimate")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Regex("/^([0-9]{1,6})$/")
     */
    protected $estimatedParticipants;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex("/^([0-9]{1,2})([\.,]*)([0-9]{0,5})$/")
     */
    protected $estimatedDistance;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex("/^([0-9]{1,2})([\.,]*)([0-9]{0,4})$/")
     */
    protected $estimatedDuration;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $creationDateTime;

    public function __construct()
    {
        $this->creationDateTime = new \DateTime();
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
     * Set estimatedParticipants
     *
     * @param integer $estimatedParticipants
     * @return RideEstimate
     */
    public function setEstimatedParticipants($estimatedParticipants)
    {
        $this->estimatedParticipants = $estimatedParticipants;

        return $this;
    }

    /**
     * Get estimatedParticipants
     *
     * @return integer 
     */
    public function getEstimatedParticipants()
    {
        return $this->estimatedParticipants;
    }

    /**
     * Set estimatedDistance
     *
     * @param float $estimatedDistance
     * @return RideEstimate
     */
    public function setEstimatedDistance($estimatedDistance)
    {
        $this->estimatedDistance = $estimatedDistance;

        return $this;
    }

    /**
     * Get estimatedDistance
     *
     * @return float 
     */
    public function getEstimatedDistance()
    {
        return $this->estimatedDistance;
    }

    /**
     * Set estimatedDuration
     *
     * @param float $estimatedDuration
     * @return RideEstimate
     */
    public function setEstimatedDuration($estimatedDuration)
    {
        $this->estimatedDuration = $estimatedDuration;

        return $this;
    }

    /**
     * Get estimatedDuration
     *
     * @return float 
     */
    public function getEstimatedDuration()
    {
        return $this->estimatedDuration;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return RideEstimate
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
     * Set user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     * @return RideEstimate
     */
    public function setUser(\Application\Sonata\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ride
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\Ride $ride
     * @return RideEstimate
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

    /**
     * Set track
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\Track $track
     * @return RideEstimate
     */
    public function setTrack(\Caldera\CriticalmassCoreBundle\Entity\Track $track = null)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * Get track
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\Track 
     */
    public function getTrack()
    {
        return $this->track;
    }
}
