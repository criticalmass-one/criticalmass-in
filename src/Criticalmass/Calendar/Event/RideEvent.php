<?php declare(strict_types=1);

namespace App\Criticalmass\Calendar\Event;

use App\Entity\Ride;
use CalendR\Event\EventInterface;
use CalendR\Event\EventTrait;

class RideEvent implements EventInterface
{
    use EventTrait;

    private \DateTime $begin;
    private \DateTime $end;
    private Ride $ride;

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

    public function isEqualTo(EventInterface $event): bool
    {
        if (!$event instanceof self) {
            return false;
        }

        if ($this->ride->getId() !== $event->getRide()->getId()) {
            return false;
        }

        return $this->getBegin() == $event->getBegin()
            && $this->getEnd() == $event->getEnd();
    }
}
