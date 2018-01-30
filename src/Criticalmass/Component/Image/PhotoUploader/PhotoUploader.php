<?php

namespace Criticalmass\Component\Image\PhotoUploader;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class PhotoUploader
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}
