<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Collector\AbstractTimelineCollector;

interface TimelineInterface
{
    const MAX_ITEMS = 100;
    
    public function addCollector(AbstractTimelineCollector $collector): TimelineInterface;

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineInterface;

    public function execute(): TimelineInterface;

    public function getTimelineContentList(): array;
}
