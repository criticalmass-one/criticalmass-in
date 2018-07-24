<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Storage;

use App\Criticalmass\Image\PhotoManipulator\Cache\PhotoCacheInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoStorage implements PhotoStorageInterface
{
    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var PhotoCacheInterface $photoCache */
    protected $photoCache;

    /** @var string $uploadDestinationPhoto */
    protected $uploadDestinationPhoto;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(UploaderHelper $uploaderHelper, PhotoCacheInterface $photoCache, RegistryInterface $registry, string $uploadDestinationPhoto)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->uploadDestinationPhoto = $uploadDestinationPhoto;
        $this->photoCache = $photoCache;
        $this->registry = $registry;
    }
}
