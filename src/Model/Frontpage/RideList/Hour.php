<?php declare(strict_types=1);

namespace App\Model\Frontpage\RideList;

use App\Entity\Ride;

class Hour implements \Iterator
{
    /** @var array $list */
    protected $list = [];

    /** @var \DateTime $dateTime */
    protected $dateTime = null;

    protected $counter = 0;

    public function __construct(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function add(Ride $ride): Hour
    {
        $this->list[] = $ride;

        return $this;
    }

    public function current()
    {
        return current($this->list);
    }

    public function next(): void
    {
        next($this->list);
    }

    public function key()
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
}
