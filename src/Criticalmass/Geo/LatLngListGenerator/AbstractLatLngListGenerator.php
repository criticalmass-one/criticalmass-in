<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\LatLngListGenerator;

use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;

abstract class AbstractLatLngListGenerator
{
    const WIDTH = 10;

    protected string $list = '';
    protected Track $track;
    protected $xmlRootNode;
    protected TrackReader $trackReader;

    public function __construct(TrackReader $trackReader)
    {
        $this->trackReader = $trackReader;
    }

    public function loadTrack(Track $track): self
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        $this->xmlRootNode = $this->trackReader->getRootNode();

        return $this;
    }

    public abstract function execute(): self;

    public function getList(): string
    {
        return $this->list;
    }
} 
