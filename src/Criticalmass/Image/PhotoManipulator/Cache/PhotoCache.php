<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Cache;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use Symfony\Component\HttpFoundation\Request;

class PhotoCache extends AbstractPhotoCache
{
    public function recachePhoto(ManipulateablePhotoInterface $photo): PhotoCacheInterface
    {
        $this->clearImageCache($photo);

        $filename = $this->uploaderHelper->asset($photo, 'imageFile');

        $this->cacheManager->remove($filename);

        $this->imagineController->filterAction(new Request(), $filename, 'gallery_photo_thumb');
        $this->imagineController->filterAction(new Request(), $filename, 'gallery_photo_standard');
        $this->imagineController->filterAction(new Request(), $filename, 'gallery_photo_large');
        $this->imagineController->filterAction(new Request(), $filename, 'city_image_wide');

        return $this;
    }

    public function clearImageCache(ManipulateablePhotoInterface $photo): PhotoCacheInterface
    {
        $path = $this->uploaderHelper->asset($photo, 'imageFile');

        $this->cacheManager->remove($path);

        return $this;
    }
}
