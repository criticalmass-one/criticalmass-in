<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Printer;

use Caldera\Bundle\CalderaBundle\MapPrinter\Canvas\Canvas;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\MarkerInterface;
use Caldera\Bundle\CalderaBundle\MapPrinter\Element\TrackInterface;

class OsmMapPrinter
{
    /** @var Canvas $canvas */
    protected $canvas = null;

    public function __construct()
    {
        $this->canvas = new Canvas();
    }

    public function addTrack(TrackInterface $track): OsmMapPrinter
    {
        $this->canvas->addTrack($track);

        return $this;
    }

    public function addMarker(MarkerInterface $marker): OsmMapPrinter
    {
        $this->canvas->addMarker($marker);

        return $this;
    }

    public function execute()
    {
        $this->canvas->calculateDimensions();
    }
}