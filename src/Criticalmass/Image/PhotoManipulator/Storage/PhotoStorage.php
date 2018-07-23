<?php declare(strict_types=1);

namespace App\Criticalmas\Image\PhotoManipulator\Storage;

use App\Criticalmas\Image\PhotoManipulator\PhotoInterface\PhotoInterface;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class PhotoStorage extends AbstractPhotoStorage
{
    public function open(PhotoInterface $photo): ImageInterface
    {
        $imagine = new Imagine();

        $image = $imagine->open($this->getImageFilename($photo));

        return $image;
    }

    public function save(PhotoInterface $photo, ImageInterface $image): string
    {
        if (!$photo->getBackupName()) {
            $newFilename = uniqid().'.JPG';

            $photo->setBackupName($photo->getImageName());

            $photo->setImageName($newFilename);
        }

        $this->photoCache->recachePhoto($photo);

        $filename = $this->getImageFilename($photo);
        $image->save($filename);

        return $filename;
    }

    protected function getImageFilename(PhotoInterface $photo): string
    {
        $path = $this->uploaderHelper->asset($photo, 'imageFile');

        $filename = sprintf('%s%s', $this->webDirectory, $path);

        return $filename;
    }
}
