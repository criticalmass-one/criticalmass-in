<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\RideMerger;

use App\Entity\Ride;

interface RideMergerInterface
{
    public function setTargetRide(Ride $targetRide): RideMergerInterface;
    public function addSourceRide(Ride $sourceRide): RideMergerInterface;
    public function addSourceRides(array $sourceRides): RideMergerInterface;
    public function merge(): Ride;
}
