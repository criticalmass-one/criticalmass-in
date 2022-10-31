<?php declare(strict_types=1);

namespace App\Criticalmass\Calendar\Event;

use CalendR\Event\AbstractEvent;
use App\Entity\Ride;

class RideEvent extends AbstractEvent
{
    protected \DateTime $begin;
    protected \DateTime $end;

    public function __construct(protected Ride $ride)
    {
        $this->begin = $ride->getDateTime();
        $this->end = $ride->getDateTime();
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function getUid(): string
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
