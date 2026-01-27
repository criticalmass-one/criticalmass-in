<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

use Carbon\Carbon;

interface MassTrackImporterInterface
{
    public function setStartDateTime(Carbon $startDateTime): MassTrackImporterInterface;

    public function setEndDateTime(Carbon $endDateTime): MassTrackImporterInterface;

    public function execute(): array;
}