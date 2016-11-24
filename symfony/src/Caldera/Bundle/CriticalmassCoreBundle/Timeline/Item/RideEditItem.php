<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CalderaBundle\Entity\Ride;

class RideEditItem extends AbstractItem
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
     * @var string $archiveMessage
     */
    protected $archiveMessage;

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

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * @return string
     */
    public function getArchiveMessage()
    {
        return $this->archiveMessage;
    }

    /**
     * @param string $archiveMessage
     */
    public function setArchiveMessage($archiveMessage)
    {
        $this->archiveMessage = $archiveMessage;

        return $this;
    }
}