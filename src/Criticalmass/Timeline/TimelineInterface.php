<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Collector\TimelineCollectorInterface;

interface TimelineInterface
{
    const MAX_ITEMS = 100;

    public function addCollector(TimelineCollectorInterface $collector): TimelineInterface;

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineInterface;

    public function execute(): TimelineInterface;

    public function getTimelineContentList(): array;
}
