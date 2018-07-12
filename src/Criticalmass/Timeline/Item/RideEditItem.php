<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Ride;

class RideEditItem extends AbstractItem
{
    /** @var Ride $ride */
    protected $ride;

    /** @var string $rideTitle */
    protected $rideTitle;

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideEditItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): RideEditItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }
}
