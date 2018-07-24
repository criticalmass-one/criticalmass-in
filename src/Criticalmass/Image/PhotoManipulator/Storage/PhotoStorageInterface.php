<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Storage;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\PhotoInterface;
use Imagine\Image\ImageInterface;

interface PhotoStorageInterface
{
    public function open(PhotoInterface $photo): ImageInterface;
    public function save(PhotoInterface $photo, ImageInterface $image): string;
}
