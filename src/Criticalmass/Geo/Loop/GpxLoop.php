<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use App\Criticalmass\Geo\GpxReader\GpxReaderInterface;

class GpxLoop extends Loop
{
    /** @var GpxReaderInterface $gpxReader */
    protected $gpxReader;

    public function __construct(GpxReaderInterface $gpxReader)
    {
        $this->gpxReader = $gpxReader;

        parent::__construct();
    }

    public function loadGpxFile(string $filename): GpxLoop
    {
        $this->positionList = $this
            ->gpxReader
            ->loadFromFile($filename)
            ->createPositionList();

        return $this;
    }
}
