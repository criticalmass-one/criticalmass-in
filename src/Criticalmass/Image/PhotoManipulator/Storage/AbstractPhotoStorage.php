<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Storage;

use App\Criticalmass\Image\PhotoManipulator\Cache\PhotoCacheInterface;
use League\Flysystem\FilesystemInterface;
use Doctrine\Persistence\ManagerRegistry;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoStorage implements PhotoStorageInterface
{
    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var PhotoCacheInterface $photoCache */
    protected $photoCache;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct(UploaderHelper $uploaderHelper, PhotoCacheInterface $photoCache, ManagerRegistry $registry, FilesystemInterface $filesystem)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->filesystem = $filesystem;
        $this->photoCache = $photoCache;
        $this->registry = $registry;
    }
}
