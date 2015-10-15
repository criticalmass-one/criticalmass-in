<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gallery\ExifReader;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;

abstract class AbstractExifReader {
    protected $photo;
    protected $exifData;
    
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;

        $this->exifData = exif_read_data($this->getPhotoFilename(), 0, true);
    }
    
    protected function getPhotoFilename()
    {
        return $this->photo->getFilePath();
    }
}