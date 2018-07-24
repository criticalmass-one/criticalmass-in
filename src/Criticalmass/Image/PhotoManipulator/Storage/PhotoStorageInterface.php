<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Storage;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use Imagine\Image\ImageInterface;

interface PhotoStorageInterface
{
    public function open(ManipulateablePhotoInterface $photo): ImageInterface;
    public function save(ManipulateablePhotoInterface $photo, ImageInterface $image): string;
}
