<?php declare(strict_types=1);

namespace Tests\PhotoGps\Mocks;

use App\Entity\Photo;

class GpsPhoto extends Photo
{
    public function getImageName(): ?string
    {
        return __DIR__.'/../data/coords.jpeg';
    }
}