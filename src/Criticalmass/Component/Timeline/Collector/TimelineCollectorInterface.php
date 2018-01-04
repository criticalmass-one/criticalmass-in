<?php

namespace Criticalmass\Component\Timeline\Collector;

interface TimelineCollectorInterface
{
    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineCollectorInterface;
    public function execute(): TimelineCollectorInterface;
    public function getItems(): array;
}
