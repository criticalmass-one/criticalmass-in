<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\FeatureManager;

use App\Criticalmass\Feature\Feature\FeatureInterface;

class FeatureManager implements FeatureManagerInterface
{
    /** @var array $featureList */
    protected $featureList = [];

    public function addFeature(FeatureInterface $feature): FeatureManagerInterface
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
