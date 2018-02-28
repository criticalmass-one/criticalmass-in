<?php

namespace Criticalmass\Component\Calendar\EventProvider;

use CalendR\Event\Provider\ProviderInterface;
use Criticalmass\Component\Calendar\Event\Event;

class RideProvider implements ProviderInterface
{
    public function getEvents(\DateTime $begin, \DateTime $end, array $options = []): array
    {
        $dateTime = new \DateTime('2018-02-23');

        return [new Event('wdqwd', $dateTime, $dateTime)];
    }
}
