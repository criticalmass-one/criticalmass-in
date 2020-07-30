<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\RideMerger;

use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRideMerger implements RideMergerInterface
{
    protected Ride $targetRide;

    protected array $sourceRides = [];

    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

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
