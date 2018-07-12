<?php declare(strict_types=1);

namespace App\Feature;

class PhotosFeature extends AbstractFeature
{
    public function __construct(bool $featurePhotos)
    {
        $this->enabled = $featurePhotos;
    }
}
