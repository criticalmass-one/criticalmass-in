<?php declare(strict_types=1);

namespace App\Feature;

abstract class AbstractFeature implements FeatureInterface
{
    protected $enabled = false;

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function getName(): string
    {
        $featureClassname = get_class($this);

        preg_match('/(.*)\\\([A-Za-z].*)Feature/', $featureClassname, $matches);

        $featureName = array_pop($matches);

        return strtolower($featureName);
    }
}
