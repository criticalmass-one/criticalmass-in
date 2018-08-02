<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use App\Criticalmass\Geo\GpxReader\GpxReader;

class GpxTimeShifter extends TimeShifter
{
    /** @var GpxReader $gpxReader */
    protected $gpxReader;

    public function __construct(GpxReader $gpxReader)
    {
        $this->gpxReader = $gpxReader;
    }

    public function loadGpxFile(string $filename): GpxTimeShifter
    {
        $this->gpxReader->loadFromFile($filename);

        $this->positionList = $this->gpxReader->createPositionList();

        return $this;
    }
}
