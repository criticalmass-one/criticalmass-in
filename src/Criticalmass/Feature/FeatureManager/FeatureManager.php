<?php declare(strict_types=1);

namespace App\Criticalmass\Feature\FeatureManager;

use App\Criticalmass\Feature\Feature\FeatureInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FeatureManager implements FeatureManagerInterface
{
    const PARAMETER_NAME_PATTERN = 'feature.%s';

    /** @var array $featureList */
    protected $featureList = [];

    /** @var ParameterBagInterface $parameterBag */
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function addFeature(FeatureInterface $feature): FeatureManagerInterface
    {
        $feature->setEnabled($this->getFeatureStatus($feature));

        $this->featureList[$feature->getName()] = $feature;

        return $this;
    }

    protected function getFeatureStatus(FeatureInterface $feature): bool
    {
        $parameterName = sprintf(self::PARAMETER_NAME_PATTERN, $feature->getName());
        
        try {
            if ($this->parameterBag->has($parameterName)) {
                return (bool) $this->parameterBag->get($parameterName);
            }
        } catch (\Exception $exception) {
            return false;
        }

        return false;
    }

    public function isFeatureEnabled(string $featureName): bool
    {
        if (!array_key_exists($featureName, $this->featureList)) {
            return false;
        }

        return $this->featureList[$featureName]->isEnabled();
    }
}
