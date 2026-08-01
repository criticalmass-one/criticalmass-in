<?php declare(strict_types=1);

namespace Tests\Criticalmass\Image\PhotoManipulator\Cache;

use App\Criticalmass\Image\PhotoManipulator\Cache\PhotoCache;
use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Regression coverage for #1438: rotating/censoring a photo returned a 404 while
 * the image was already written, because {@see PhotoCache::recachePhoto()} warmed
 * the LiipImagine cache with the `city_image_wide` filter — whose data loader
 * reads from the city filesystem, where a photo file never exists. LiipImagine
 * turns the resulting NotLoadableException into a NotFoundHttpException, which
 * bubbled up as the response of the user action.
 */
class PhotoCacheTest extends TestCase
{
    private function createUploaderHelper(string $uri): UploaderHelper
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->method('resolveUri')->willReturn($uri);

        return new UploaderHelper($storage);
    }

    public function testRecacheNeverWarmsWithTheCityImageFilter(): void
    {
        $usedFilters = [];

        $imagineController = $this->createMock(ImagineController::class);
        $imagineController->method('filterAction')
            ->willReturnCallback(function ($request, $path, $filter) use (&$usedFilters): RedirectResponse {
                $usedFilters[] = $filter;

                return new RedirectResponse('/');
            });

        $photoCache = new PhotoCache(
            $this->createUploaderHelper('/photos/example.jpg'),
            $this->createMock(CacheManager::class),
            $imagineController,
        );

        $photoCache->recachePhoto($this->createMock(ManipulateablePhotoInterface::class));

        self::assertNotContains('city_image_wide', $usedFilters, 'A photo must never be warmed with the city filter');
        self::assertContains('gallery_photo_thumb', $usedFilters);
        self::assertContains('gallery_photo_standard', $usedFilters);
        self::assertContains('gallery_photo_large', $usedFilters);
    }

    public function testRecacheDoesNotBubbleFilterFailures(): void
    {
        $imagineController = $this->createMock(ImagineController::class);
        $imagineController->method('filterAction')
            ->willThrowException(new NotFoundHttpException('Source image could not be found'));

        $photoCache = new PhotoCache(
            $this->createUploaderHelper('/photos/example.jpg'),
            $this->createMock(CacheManager::class),
            $imagineController,
        );

        // Warming the cache is best-effort: a failing filter must never turn a
        // successful manipulation into an error response.
        $result = $photoCache->recachePhoto($this->createMock(ManipulateablePhotoInterface::class));

        self::assertSame($photoCache, $result);
    }
}
