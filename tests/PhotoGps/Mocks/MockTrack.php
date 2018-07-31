<?php declare(strict_types=1);

namespace Tests\PhotoGps\Mocks;

use App\Entity\Track;

class MockTrack extends Track
{
    public function getTrackFilename(): ?string
    {
        return '../tests/PhotoGps/Files/braunschweig.gpx';
    }
}
