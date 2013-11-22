<?php

namespace Caldera\CriticalmassGalleryBundle\Utility;

class ImageDirectory
{
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function getImageFileArray()
    {
        $resultArray = array();

        if ($dh = opendir($this->directory))
        {
            while (($file = readdir($dh)) !== false)
            {
                if (($file != ".") && ($file != "..") && (strpos($file, ".jpg") == strlen($file) - 4))
                {
                    $resultArray[] = $file;
                }
            }

            closedir($dh);
        }

        return $resultArray;
    }

} 