<?php

namespace Caldera\Bundle\CalderaBundle\Gps\TrackChecker;

use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CalderaBundle\Gps\GpxReader\TrackReader;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackChecker implements TrackCheckerInterface
{
    protected $track;
    protected $result = true;

    public function __construct(UploaderHelper $uploaderHelper, TrackReader $trackReader, $rootDirectory)
    {


    }

    public function loadTrack(Track $track)
    {
        $this->track = $track;
    }

    public function isValid()
    {
        return $this->result;
    }

    protected function checkPointsNumber()
    {


    }


}