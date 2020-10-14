<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use App\Criticalmass\Geo\Converter\GpxToPositionListConverter;

class GpxLoop extends Loop
{
    /** @var GpxToPositionListConverter $gpxToPositionListConverter */
    protected $gpxToPositionListConverter;

    public function __construct(GpxToPositionListConverter $gpxToPositionListConverter)
    {
        $this->gpxToPositionListConverter = $gpxToPositionListConverter;

        parent::__construct();
    }

    public function loadGpxFile(string $filename): GpxLoop
    {
        $this->positionList = $this->gpxToPositionListConverter->convert($filename);

        return $this;
    }
}
