<?php declare(strict_types=1);

namespace Tests\Image\PhotoGps\Mocks;

use AppBundle\Entity\Track;

class MockTrack extends Track
{
    public function getTrackFilename(): ?string
    {
        return '../../tests/Image/PhotoGps/Files/braunschweig.gpx';
    }
}
