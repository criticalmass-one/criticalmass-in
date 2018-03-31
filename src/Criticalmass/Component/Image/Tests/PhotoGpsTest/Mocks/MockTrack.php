<?php

namespace Criticalmass\Component\Image\Tests\PhotoGpsTest\Mocks;

use Criticalmass\Bundle\AppBundle\Entity\Track;

class MockTrack extends Track
{
    public function getTrackFilename(): ?string
    {
        return '../../src/Criticalmass/Component/Image/Tests/PhotoGpsTest/Files/braunschweig.gpx';
    }
}
