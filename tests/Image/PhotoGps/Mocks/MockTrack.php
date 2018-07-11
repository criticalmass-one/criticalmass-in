<?php

namespace Tests\Component\Image\PhotoGps\Mocks;

use Criticalmass\Bundle\AppBundle\Entity\Track;

class MockTrack extends Track
{
    public function getTrackFilename(): ?string
    {
        return '../../tests/Component/Image/PhotoGps/Files/braunschweig.gpx';
    }
}
