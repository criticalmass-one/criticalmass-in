<?php declare(strict_types=1);

namespace App\Model\Frontpage\RideList;

use App\Entity\Ride;
use Carbon\Carbon;

class Day implements \Iterator
{
    protected ?Carbon $dateTime = null;

    protected array $list = [];

    public function __construct(Carbon $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function addRide(Ride $ride): Day
    {
        $this->list[] = $ride;

        return $this;
    }

    public function current(): Ride
    {
        return current($this->list);
    }

    public function next(): void
    {
        next($this->list);
    }

    public function key(): int
    {
        return key($this->list);
    }

    public function valid(): bool
    {
        return current($this->list) instanceof Ride;
    }

    public function rewind(): void
    {
        reset($this->list);
    }

    public function getDateTime(): Carbon
    {
        return $this->dateTime;
    }

    public function sort(): Day
    {
        usort($this->list, function(Ride $a, Ride $b): int
        {
            if ($a->getCity()->getCity() === $b->getCity()->getCity() ) {
                return 0;
            }

            return $a->getCity()->getCity()  < $b->getCity()->getCity() ? -1 : 1;
        });

        return $this;
    }
}
