<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Collector\AbstractTimelineCollector;
use Carbon\Carbon;

interface TimelineInterface
{
    const MAX_ITEMS = 100;

    public function addCollector(AbstractTimelineCollector $collector): TimelineInterface;

    public function setDateRange(Carbon $startDateTime, Carbon $endDateTime): TimelineInterface;

    public function execute(): TimelineInterface;

    public function getTimelineContentList(): array;
}
