<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\PhotoResizer;

use Caldera\CriticalmassGalleryBundle\Entity\Photo;

class PhotoResizer {
    protected $filename;
    protected $image;
    protected $photo;

    public function loadJpeg($filename)
    {
        $this->filename = $filename;
        $this->image = imagecreatefromjpeg($filename);
    }

    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;
        $this->filename = getcwd().'/photos/'.$photo->getId().'.jpg';

        $this->image = imagecreatefromjpeg($this->filename);
    }

    public function getCurrentSize()
    {
        return getimagesize($this->filename);
    }
    
    public function resizeFactor($factor)
    {
        list($oldWidth, $oldHeight) = getimagesize($this->filename);
        
        $newWidth = $oldWidth * $factor;
        $newHeight = $oldHeight * $factor;
        
        $this->resize($newWidth, $newHeight);
    }
    
    public function resize($newWidth, $newHeight)
    {
        list($oldWidth, $oldHeight) = getimagesize($this->filename);
        
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);
        
        $this->image = $resizedImage;
    }

    public function saveJpeg($filename, $quality = 75)
    {
        imagejpeg($this->image, $filename, $quality);
    }
}