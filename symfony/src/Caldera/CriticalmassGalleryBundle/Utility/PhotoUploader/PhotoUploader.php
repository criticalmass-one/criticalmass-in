<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\PhotoUploader;

use Caldera\CriticalmassGalleryBundle\Entity\Photo;
use Caldera\CriticalmassGalleryBundle\Utility\PhotoResizer\PhotoResizer;


class PhotoUploader {
    protected $photo;

    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;
    }

    public function execute()
    {
        $this->photo->getFile()->move($this->photo->getUploadRootDir(), $this->photo->getId().'.jpg');

        if ($this->photo->getFile()->getClientOriginalExtension() == "jpg" ||
            $this->photo->getFile()->getClientOriginalExtension() == "JPG" ||
            $this->photo->getFile()->getClientOriginalExtension() == "jpeg" ||
            $this->photo->getFile()->getClientOriginalExtension() == "JPEG")
        {
            $pr = new PhotoResizer();
            $pr->setPhoto($this->photo);
            $pr->resizeFactor(0.5);
            $pr->saveJpeg($this->photo->getUploadRootDir().$this->photo->getId().'.small.jpg');
            //$smallSize = $utility->reduceSize($this, 0.5);
            //$utility->makeSmallPhotoJPG($this, $smallSize['width'], $smallSize['height'], "_klein");
            //$utility->makeSmallPhotoJPG($this, 200, 200, "_thumbnail");
            //$utility->getMetaInfos($this);

        }
    }
}