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

        $photoContent = $this->filesystem->read($this->getImageFilename($photo));

        return $imagine->load($photoContent);
    }

    public function save(ManipulateablePhotoInterface $photo, ImageInterface $image): string
    {
        if (!$photo->getBackupName()) {
            $newFilename = sprintf('%s.jpg', uniqid('', true));

            $photo->setBackupName($photo->getImageName());

            $photo->setImageName($newFilename);

            $this->registry->getManager()->flush();
        }

        $filename = $this->getImageFilename($photo);

        $this->filesystem->put($filename, $image->get('jpeg'));

        $this->photoCache->recachePhoto($photo);

        return $filename;
    }

    protected function getImageFilename(ManipulateablePhotoInterface $photo): string
    {
        return $this->uploaderHelper->asset($photo, 'imageFile');
    }
}
