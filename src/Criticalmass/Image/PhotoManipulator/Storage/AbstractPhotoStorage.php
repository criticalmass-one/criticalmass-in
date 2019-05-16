<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Storage;

use App\Criticalmass\Image\PhotoManipulator\Cache\PhotoCacheInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoStorage implements PhotoStorageInterface
{
    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var PhotoCacheInterface $photoCache */
    protected $photoCache;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(UploaderHelper $uploaderHelper, PhotoCacheInterface $photoCache, RegistryInterface $registry, FilesystemInterface $filesystem)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->filesystem = $filesystem;
        $this->photoCache = $photoCache;
        $this->registry = $registry;
    }
}
