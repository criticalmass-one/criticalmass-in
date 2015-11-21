<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoResizer;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Monolog\Logger;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class BulkPhotoResizer extends PhotoResizer
{
    protected $formatSets;
    protected $formatCollections;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory . '/../web';
    }

    public function setFormatCollections($formatCollections)
    {
        $this->formatCollections = $formatCollections;
    }
    
    public function setFormatSets($formatSets)
    {
        $this->formatSets = $formatSets;
    }
    
    public function resizeAll($set)
    {
        $collectionNames = $this->formatSets[$set];
        
        foreach ($collectionNames as $collectionName) {
            $collectionFormats = $this->formatCollections[$collectionName];
        
            foreach ($collectionFormats as $formatName => $dimensions) {
                list($width, $height) = $dimensions;

                $this->resizeBySize($width, $height);

                $this->savePhoto($formatName);
            }
        }
        
    }
}