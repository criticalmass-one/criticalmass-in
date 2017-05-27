<?php

namespace AppBundle\Image\Filter;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class BlurFilter implements LoaderInterface
{
    public function load(ImageInterface $image, array $options = array())
    {
        $image
            ->effects()
            ->blur(5)
        ;

        return $image;
    }
}