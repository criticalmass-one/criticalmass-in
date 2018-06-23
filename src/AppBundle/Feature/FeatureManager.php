<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Feature;

class FeatureManager
{
    protected $featureList = [];

    public function addFeature(FeatureInterface $feature): FeatureManager
    {
        $this->featureList[$feature->getName()] = $feature;

        return $this;
    }

    public function isFeatureEnabled(string $featureName): bool
    {
        if (!array_key_exists($featureName, $this->featureList)) {
            return false;
        }

        return $this->featureList[$featureName]->enabled();
    }
}
