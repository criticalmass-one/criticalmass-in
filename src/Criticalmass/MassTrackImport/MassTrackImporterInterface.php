<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

interface MassTrackImporterInterface
{
    public function setStartDateTime(\DateTime $startDateTime): MassTrackImporterInterface;

    public function setEndDateTime(\DateTime $endDateTime): MassTrackImporterInterface;

    public function execute(): array;
}