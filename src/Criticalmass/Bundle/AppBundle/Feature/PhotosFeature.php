<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Feature;

class PhotosFeature extends AbstractFeature
{
    public function __construct(bool $featurePhotos)
    {
        $this->enabled = $featurePhotos;
    }
}
