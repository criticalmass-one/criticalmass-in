<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use App\Criticalmass\Geo\Converter\GpxToPositionListConverter;

class GpxTimeShifter extends TimeShifter implements GpxTimeShifterInterface
{
    /** @var GpxToPositionListConverter $gpxToPositionListConverter */
    protected $gpxToPositionListConverter;

    public function __construct(GpxToPositionListConverter $gpxToPositionListConverter)
    {
        $this->gpxToPositionListConverter = $gpxToPositionListConverter;
    }

    public function loadGpxFile(string $filename): GpxTimeShifterInterface
    {
        $this->positionList = $this->gpxToPositionListConverter->convert($filename);

        return $this;
    }
}
