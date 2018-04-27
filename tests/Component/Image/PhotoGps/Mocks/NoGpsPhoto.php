<?php

namespace Tests\Component\Image\PhotoGps\Mocks;

use Criticalmass\Bundle\AppBundle\Entity\Photo;

class NoGpsPhoto extends Photo
{
    public function getImageName(): ?string
    {
        return '../../tests/Component/Image/PhotoGps/Files/no-coords.jpeg';
    }
}
