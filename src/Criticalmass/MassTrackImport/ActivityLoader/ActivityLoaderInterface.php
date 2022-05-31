<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ActivityLoader;

interface ActivityLoaderInterface
{
    public function load(): array;

    public function setStartDateTime(\DateTime $startDateTime): ActivityLoaderInterface;

    public function setEndDateTime(\DateTime $endDateTime): ActivityLoaderInterface;
}
