<?php declare(strict_types=1);

namespace Tests\PhotoGps\Mocks;

use App\Entity\Photo;

class NoGpsPhoto extends Photo
{
    public function getImageName(): ?string
    {
        return '../tests/PhotoGps/Files/no-coords.jpeg';
    }
}
