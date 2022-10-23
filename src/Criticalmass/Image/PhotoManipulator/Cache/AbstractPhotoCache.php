<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Cache;

use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoCache implements PhotoCacheInterface
{
    public function __construct(protected UploaderHelper $uploaderHelper, protected CacheManager $cacheManager, protected ImagineController $imagineController)
    {
    }
}
