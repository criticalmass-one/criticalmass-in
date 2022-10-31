<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use App\Criticalmass\Geo\Converter\GpxToPositionListConverter;

class GpxLoop extends Loop
{
    public function __construct(protected GpxToPositionListConverter $gpxToPositionListConverter)
    {
        parent::__construct();
    }

    public function loadGpxFile(string $filename): GpxLoop
    {
        $this->positionList = $this->gpxToPositionListConverter->convert($filename);

        return $this;
    }
}
