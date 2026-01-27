<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use Carbon\Carbon;

interface TimelineCollectorInterface
{
    public function setDateRange(Carbon $startDateTime, Carbon $endDateTime): TimelineCollectorInterface;

    public function execute(): TimelineCollectorInterface;

    public function getItems(): array;

    public function getRequiredFeatures(): array;
}
