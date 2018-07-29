<?php declare(strict_types=1);

namespace App\Criticalmass\Gps\PolylineGenerator;

use App\Entity\Track;
use App\Criticalmass\Gps\GpxReader\TrackReader;

/**
 * @deprecated
 */
abstract class AbstractPolylineGenerator implements PolylineGeneratorInterface
{
    /** @var Track $track */
    protected $track;

    /** @var string $polyline */
    protected $polyline = null;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var int $gapWidth */
    protected $gapWidth;

    public function __construct(TrackReader $trackReader, int $gapWidth)
    {
        $this->trackReader = $trackReader;

        $this->gapWidth = $gapWidth;
    }

    public function loadTrack(Track $track): PolylineGeneratorInterface
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        return $this;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }
}
