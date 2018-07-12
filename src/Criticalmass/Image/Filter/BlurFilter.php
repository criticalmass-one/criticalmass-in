<?php

namespace App\Criticalmass\Image\Filter;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class BlurFilter implements LoaderInterface
{
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        $image
            ->effects()
            ->blur(5);

        return $image;
    }
}
