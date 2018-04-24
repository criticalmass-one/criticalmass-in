<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Feature;

class PhotosFeature implements FeatureInterface
{
    protected $enabled = false;

    public function __construct(bool $featurePhotos)
    {
        $this->enabled = $featurePhotos;
    }

    public function getName(): string
    {
        return 'photos';
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }
}
