<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\Storage;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class PhotoStorage extends AbstractPhotoStorage
{
    public function open(ManipulateablePhotoInterface $photo): ImageInterface
    {
        $imagine = new Imagine();

        $image = $imagine->open($this->getImageFilename($photo));

        return $image;
    }

    public function save(ManipulateablePhotoInterface $photo, ImageInterface $image): string
    {
        if (!$photo->getBackupName()) {
            $newFilename = uniqid().'.JPG';

            $photo->setBackupName($photo->getImageName());

            $photo->setImageName($newFilename);

            $this->registry->getManager()->flush();
        }

        $filename = $this->getImageFilename($photo);
        $image->save($filename);

        $this->photoCache->recachePhoto($photo);

        return $filename;
    }

    protected function getImageFilename(ManipulateablePhotoInterface $photo): string
    {
        $path = $this->uploaderHelper->asset($photo, 'imageFile');

        $filename = sprintf('%s/..%s', $this->uploadDestinationPhoto, $path);

        return $filename;
    }
}
