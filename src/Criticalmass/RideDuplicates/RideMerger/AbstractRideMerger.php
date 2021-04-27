<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\RideMerger;

use App\Entity\Ride;

abstract class AbstractRideMerger implements RideMergerInterface
{
    protected Ride $targetRide;

    protected array $sourceRides = [];

    public function setTargetRide(Ride $targetRide): RideMergerInterface
    {
        $this->targetRide = $targetRide;

        return $this;
    }

    public function addSourceRide(Ride $sourceRide): RideMergerInterface
    {
        $this->sourceRides[$sourceRide->getId()] = $sourceRide;

        return $this;
    }

    public function addSourceRides(array $sourceRides): RideMergerInterface
    {
        foreach ($sourceRides as $sourceRide) {
            $this->addSourceRide($sourceRide);
        }

        return $this;
    }
}
