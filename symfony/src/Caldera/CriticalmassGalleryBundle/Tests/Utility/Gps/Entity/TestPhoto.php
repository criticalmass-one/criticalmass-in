<?php

namespace Caldera\CriticalmassGalleryBundle\Tests\Utility\Gps\Entity;

use Caldera\CriticalmassGalleryBundle\Entity\Photo;

class TestPhoto extends Photo
{
    protected $filePath;
    
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }
    
    public function getFilePath()
    {
        return $this->filePath;
    }
}
