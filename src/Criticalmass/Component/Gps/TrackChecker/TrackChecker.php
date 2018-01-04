<?php

namespace Criticalmass\Component\Gps\TrackChecker;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Gps\GpxReader\TrackReader;
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
