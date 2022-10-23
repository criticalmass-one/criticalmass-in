<?php declare(strict_types=1);

namespace App\Model\Frontpage\RideList;

use App\Entity\Ride;

class Day implements \Iterator
{
    protected array $list = [];

    public function __construct(protected ?\DateTime $dateTime)
    {
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

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function sort(): Day
    {
        usort($this->list, fn(Ride $a, Ride $b): int => $a->getCity()->getCity() <=> $b->getCity()->getCity());

        return $this;
    }
}
