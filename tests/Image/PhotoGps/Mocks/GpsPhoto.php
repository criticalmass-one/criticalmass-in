<?php declare(strict_types=1);

namespace Tests\Image\PhotoGps\Mocks;

use App\Entity\Photo;

class GpsPhoto extends Photo
{
    public function getImageName(): ?string
    {
        return '../../tests/Image/PhotoGps/Files/coords.jpeg';
    }
}
