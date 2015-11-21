<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoResizer;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PhotoResizer extends ImageResizer
{
    protected $uploaderHelper;
    protected $rootDirectory;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory . '/../web';
    }
    
    public function loadPhoto(Photo $photo)
    {
        $filename = $this->uploaderHelper->asset($photo, 'imageFile');

        $this->loadJpeg($this->rootDirectory . $filename);
    }

    public function savePhoto($type = null, $quality = 75)
    {
        $this->saveJpeg($type, $quality);
    }
}