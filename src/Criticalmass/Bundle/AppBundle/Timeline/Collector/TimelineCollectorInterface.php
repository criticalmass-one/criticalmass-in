<?php

namespace Criticalmass\Bundle\AppBundle\Timeline\Collector;

interface TimelineCollectorInterface
{
    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineCollectorInterface;
    public function execute(): TimelineCollectorInterface;
    public function getItems(): array;
}
