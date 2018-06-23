<?php

namespace Tests\Component\Image\PhotoGps\Mocks;

use Criticalmass\Bundle\AppBundle\Entity\Photo;

class GpsPhoto extends Photo
{
    public function getImageName(): ?string
    {
        return '../../tests/Component/Image/PhotoGps/Files/coords.jpeg';
    }
}
