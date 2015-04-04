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

        $this->makeSmallPhoto();
        $this->makeThumbnail();
    }
    
    protected function makeSmallPhoto()
    {
        $pr = new PhotoResizer();
        $pr->setPhoto($this->photo);
        $pr->resizeFactor(0.5);
        $pr->saveJpeg($this->photo->getUploadRootDir().$this->photo->getId().'.small.jpg');
    }
    
    protected function makeThumbnail()
    {
        $pr = new PhotoResizer();
        $pr->setPhoto($this->photo);
        $pr->resizeFactor(0.2);
        $pr->saveJpeg($this->photo->getUploadRootDir().$this->photo->getId().'.thumbnail.jpg');
    }
}