<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Storage;

use App\Criticalmass\Image\PhotoManipulator\Cache\PhotoCacheInterface;
use League\Flysystem\FilesystemOperator;
use Doctrine\Persistence\ManagerRegistry;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoStorage implements PhotoStorageInterface
{
    public function __construct(
        protected UploaderHelper $uploaderHelper,
        protected PhotoCacheInterface $photoCache,
        protected ManagerRegistry $registry,
        protected FilesystemOperator $filesystem
    ) {
    }
}
