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

        // Only warm the filters that actually load from the photo filesystem.
        // The former `city_image_wide` call used the city loader, so a photo path
        // was never found there and LiipImagine turned that into a 404 — which
        // broke the rotate/censor action even though the image had already been
        // written (#1438).
        foreach (['gallery_photo_thumb', 'gallery_photo_standard', 'gallery_photo_large'] as $filter) {
            try {
                $this->imagineController->filterAction(new Request(), $filename, $filter);
            } catch (\Throwable) {
                // Warming the cache is best-effort: a failing filter must never
                // turn a successful manipulation into an error response. The
                // derivative is regenerated lazily on the next request.
            }
        }

        return $this;
    }

    public function clearImageCache(ManipulateablePhotoInterface $photo): PhotoCacheInterface
    {
        $path = $this->uploaderHelper->asset($photo, 'imageFile');

        $this->cacheManager->remove($path);

        return $this;
    }
}
