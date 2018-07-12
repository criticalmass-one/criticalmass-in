<?php

namespace App\Criticalmass\Image\Filter;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class FastBlurFilter implements LoaderInterface
{
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        $dimension = $image->getSize();
        $pixelateDimension = $dimension->scale(0.15);

        $image
            ->resize($pixelateDimension, ImageInterface::FILTER_CUBIC)
            ->resize($dimension, ImageInterface::FILTER_CUBIC);

        return $image;
    }
}
