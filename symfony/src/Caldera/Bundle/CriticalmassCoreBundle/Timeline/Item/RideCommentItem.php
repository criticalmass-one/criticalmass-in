<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CalderaBundle\Entity\Ride;

class RideCommentItem extends AbstractItem
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
     * @var string $text
     */
    protected $text;

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

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

}