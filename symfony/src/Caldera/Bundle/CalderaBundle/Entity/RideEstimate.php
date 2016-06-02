<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CriticalmassModelBundle\Repository\RideEstimateRepository")
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
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="estimates", fetch="LAZY")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * Tour, zu der diese Entitaet abgespeichert wurde.
	 *
	 * @ORM\ManyToOne(targetEntity="Ride", inversedBy="estimates", fetch="LAZY")
	 * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
	 */
	protected $ride;

    /**
     * @ORM\OneToOne(targetEntity="Track", mappedBy="rideEstimate", cascade={"persist"}, fetch="LAZY")
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
     * @param User $user
     * @return RideEstimate
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ride
     *
     * @param Ride $ride
     * @return RideEstimate
     */
    public function setRide(Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return Ride
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Set track
     *
     * @param Track $track
     * @return RideEstimate
     */
    public function setTrack(Track $track = null)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * Get track
     *
     * @return Track
     */
    public function getTrack()
    {
        return $this->track;
    }
}
