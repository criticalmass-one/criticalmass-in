<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Cache;

use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoCache implements PhotoCacheInterface
{
    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var CacheManager $cacheManager */
    protected $cacheManager;

    /** @var ImagineController $imagineController */
    protected $imagineController;

    public function __construct(UploaderHelper $uploaderHelper, CacheManager $cacheManager, ImagineController $imagineController)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
        $this->imagineController = $imagineController;
    }
}
