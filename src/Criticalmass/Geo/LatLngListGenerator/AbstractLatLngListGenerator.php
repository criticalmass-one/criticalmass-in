<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\LatLngListGenerator;

use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;

abstract class AbstractLatLngListGenerator
{
    protected $list;

    /**
     * @var Track $track
     */
    protected $track;
    protected $xmlRootNode;

    public function __construct(protected TrackReader $trackReader, protected $gapWidth)
    {
    }

    public function loadTrack(Track $track)
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        $this->xmlRootNode = $this->trackReader->getRootNode();

        return $this;
    }

    public abstract function execute();

    public function getList()
    {
        return $this->list;
    }
} 
