<?php declare(strict_types=1);

namespace Tests\PhotoGps\Mocks;

use App\Entity\Track;

class MockTrack extends Track
{
    public function getTrackFilename(): ?string
    {
        return __DIR__.'/../data/braunschweig.gpx';
    }
}