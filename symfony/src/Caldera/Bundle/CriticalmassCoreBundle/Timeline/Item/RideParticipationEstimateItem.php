<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Caldera\Bundle\CriticalmassModelBundle\Entity\User;

class RideParticipationEstimateItem extends AbstractItem
{
    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var Ride $ride
     */
    protected $ride;

    /**
     * @var string $rideTitle
     */
    protected $rideTitle;

    /**
     * @var integer $estimatedParticipants
     */
    protected $estimatedParticipants;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return Ride
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * @param Ride $ride
     */
    public function setRide($ride)
    {
        $this->ride = $ride;
    }

    /**
     * @return string
     */
    public function getRideTitle()
    {
        return $this->rideTitle;
    }

    /**
     * @param string $rideTitle
     */
    public function setRideTitle($rideTitle)
    {
        $this->rideTitle = $rideTitle;
    }

    /**
     * @return int
     */
    public function getEstimatedParticipants()
    {
        return $this->estimatedParticipants;
    }

    /**
     * @param int $estimatedParticipants
     */
    public function setEstimatedParticipants($estimatedParticipants)
    {
        $this->estimatedParticipants = $estimatedParticipants;
    }
}