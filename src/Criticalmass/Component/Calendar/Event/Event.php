<?php

namespace Criticalmass\Component\Calendar\Event;

use CalendR\Event\AbstractEvent;

class Event extends AbstractEvent
{
    /** @var string $uid */
    protected $uid;

    /** @var \DateTime $begin */
    protected $begin;

    /** @var \DateTime $end */
    protected $end;

    public function __construct(string $uid, \DateTime $start, \DateTime $end)
    {
        $this->uid = $uid;
        $this->begin = clone $start;
        $this->end = clone $end;
    }

    public function getUid(): string
    {
        return $this->uid;
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
