<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Ride;

class RideEditItem extends AbstractItem
{
    /** @var Ride $ride */
    protected $ride;

    /** @var string $rideTitle */
    protected $rideTitle;

    /** @var bool $enabled */
    protected $enabled;

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

    public function setEnabled(bool $enabled): RideEditItem
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
