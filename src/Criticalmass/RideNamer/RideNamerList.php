<?php declare(strict_types=1);

namespace App\Criticalmass\RideNamer;

class RideNamerList implements RideNamerListInterface
{
    /** @var array $list */
    protected $list;

    public function addRideNamer(RideNamerInterface $rideNamer): RideNamerListInterface
    {
        $this->list[get_class($rideNamer)] = $rideNamer;
        
        return $this;
    }
    
    public function getList(): array
    {
        return $this->list;
    }
}
