<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ActivityLoader;

use Carbon\Carbon;

interface ActivityLoaderInterface
{
    public function load(): array;

    public function setStartDateTime(Carbon $startDateTime): ActivityLoaderInterface;

    public function setEndDateTime(Carbon $endDateTime): ActivityLoaderInterface;
}
