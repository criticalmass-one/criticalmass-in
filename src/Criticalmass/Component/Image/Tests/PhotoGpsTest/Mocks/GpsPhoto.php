<?php

namespace Criticalmass\Component\Image\Tests\PhotoGpsTest\Mocks;

use Criticalmass\Bundle\AppBundle\Entity\Photo;

class GpsPhoto extends Photo
{
    public function getImageName(): ?string
    {
        return '../../src/Criticalmass/Component/Image/Tests/PhotoGpsTest/Files/coords.jpeg';
    }
}
