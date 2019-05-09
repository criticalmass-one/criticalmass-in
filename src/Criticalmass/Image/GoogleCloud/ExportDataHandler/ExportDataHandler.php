<?php declare(strict_types=1);

namespace App\Criticalmass\Image\GoogleCloud\ExportDataHandler;

use App\Entity\Photo;

class ExportDataHandler implements ExportDataHandlerInterface
{
    /** @var string $uploadDestinationPhoto */
    protected $uploadDestinationPhoto;

    public function __construct(string $uploadDestinationPhoto)
    {
        $this->uploadDestinationPhoto = $uploadDestinationPhoto;
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
        $filename = sprintf('%s/%s', $this->uploadDestinationPhoto, $photo->getImageName());

        if (!file_exists($filename)) {
            return $photo;
        }

        $photo
            ->setImageMimeType(mime_content_type($filename))
            ->setImageSize(filesize($filename))
            ->setImageGoogleCloudHash(base64_encode(md5_file($filename)));

        return $photo;
    }

    protected function calculateBackupProperties(Photo $photo): Photo
    {
        $filename = sprintf('%s/%s', $this->uploadDestinationPhoto, $photo->getBackupName());

        if (!file_exists($filename)) {
            return $photo;
        }

        $photo->setImageGoogleCloudHash(base64_encode(md5_file($filename)));

        return $photo;
    }
}