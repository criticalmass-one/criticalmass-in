<?php declare(strict_types=1);

namespace App\Criticalmas\Image\PhotoManipulator\Cache;

use App\Criticalmas\Image\PhotoManipulator\PhotoInterface\PhotoInterface;

interface PhotoCacheInterface
{
    public function recachePhoto(PhotoInterface $photo): PhotoCacheInterface;
    public function clearImageCache(PhotoInterface $photo): PhotoCacheInterface;
}
