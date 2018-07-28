<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\Feature;

class PhotosFeature extends AbstractFeature
{
    public function __construct(bool $featurePhotos)
    {
        $this->enabled = $featurePhotos;
    }
}
