<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoResizer;

class ImageResizer extends AbstractImageResizer
{
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

    public function resizeByFactor($factor)
    {
        list($oldWidth, $oldHeight) = getimagesize($this->filename);

        $newWidth = $oldWidth * $factor;
        $newHeight = $oldHeight * $factor;

        $this->resizeBySize($newWidth, $newHeight);
    }

    public function resizeBySize($newWidth, $newHeight)
    {
        list($oldWidth, $oldHeight) = getimagesize($this->filename);

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);

        $this->image = $resizedImage;
    }

    public function saveJpeg($type = null, $quality = 75)
    {
        if ($type) {
            $filenameParts = explode('.', $this->filename);

            $newFilenameParts = array_slice($filenameParts, 0, count($filenameParts) - 1, true) +
                                array('type' => $type) +
                                array_slice($filenameParts, count($filenameParts) - 1, 1, true);

            $filename = implode('.', $newFilenameParts);
        } else {
            $filename = $this->filename;
        }
        
        imagejpeg($this->image, $filename, $quality);
    }
}