<?php

namespace App\Criticalmass\Calendar\Event;

use CalendR\Event\AbstractEvent;
use App\Entity\Ride;

class RideEvent extends AbstractEvent
{
    /** @var \DateTime $begin */
    protected $begin;

    /** @var \DateTime $end */
    protected $end;

    /** @var Ride $ride */
    protected $ride;

    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
        $this->begin = $ride->getDateTime();
        $this->end = $ride->getDateTime();
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function getUid()
    {
        $uid = sprintf('ride-%d', $this->ride->getId());

        return $uid;
    }

    public function getBegin(): \DateTime
    {
        return $this->begin;
    }

    public function getEnd(): \DateTime
    {
        return $this->end;
    }
}
