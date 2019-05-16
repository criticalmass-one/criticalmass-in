<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Cache;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;

interface PhotoCacheInterface
{
    public function recachePhoto(ManipulateablePhotoInterface $photo): PhotoCacheInterface;
    public function clearImageCache(ManipulateablePhotoInterface $photo): PhotoCacheInterface;
}
