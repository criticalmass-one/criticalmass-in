<?php declare(strict_types=1);

namespace App\Criticalmass\Image\GoogleCloud\ExportDataHandler;

use App\Entity\Photo;
use League\Flysystem\FilesystemInterface;

class ExportDataHandler implements ExportDataHandlerInterface
{
    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function calculateForPhoto(Photo $photo): Photo
    {
        if ($photo->getImageName()) {
            $photo = $this->calculateImageProperties($photo);

            if ($photo->getBackupName()) {
                $photo = $this->calculateBackupProperties($photo);
            }
        }

        return $photo;
    }

    protected function calculateImageProperties(Photo $photo): Photo
    {
        if (!$this->filesystem->has($photo->getImageName())) {
            return $photo;
        }

        $photo
            ->setImageMimeType($this->filesystem->getMimetype($photo->getImageName()))
            ->setImageSize($this->filesystem->getSize($photo->getImageName()))
            ->setImageGoogleCloudHash(base64_encode(md5($this->filesystem->read($photo->getImageName()))));

        return $photo;
    }

    protected function calculateBackupProperties(Photo $photo): Photo
    {
        if (!$this->filesystem->has($photo->getBackupName())) {
            return $photo;
        }

        $photo->setImageGoogleCloudHash(base64_encode(md5($this->filesystem->read($photo->getBackupName()))));

        return $photo;
    }
}