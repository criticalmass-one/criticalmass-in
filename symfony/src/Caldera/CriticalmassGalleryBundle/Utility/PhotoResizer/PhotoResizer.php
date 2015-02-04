<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 18:14
 */

namespace Caldera\CriticalmassGalleryBundle\Utility\PhotoResizer;


class PhotoResizer {
    protected $filename;
    protected $image;

    public function loadJpeg($filename)
    {
        $this->filename = $filename;
        $this->image = imagecreatefromjpeg($filename);
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