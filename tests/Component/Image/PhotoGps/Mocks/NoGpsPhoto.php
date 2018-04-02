<?php

namespace Criticalmass\Component\Image\Tests\PhotoGpsTest\Mocks;

use Criticalmass\Bundle\AppBundle\Entity\Photo;

class NoGpsPhoto extends Photo
{
    public function getImageName(): ?string
    {
        return '../../src/Criticalmass/Component/Image/Tests/PhotoGpsTest/Files/no-coords.jpeg';
    }
}
