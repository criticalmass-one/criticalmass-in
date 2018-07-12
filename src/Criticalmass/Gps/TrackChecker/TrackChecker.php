<?php

namespace App\Criticalmass\Gps\TrackChecker;

use App\Entity\Track;
use App\Criticalmass\Gps\GpxReader\TrackReader;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @deprecated
 */
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
