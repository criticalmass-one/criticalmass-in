<?php declare(strict_types=1);

namespace AppBundle\Twig\Extension;

use AppBundle\Feature\FeatureManager;

class FeatureTwigExtension extends \Twig_Extension
{
    /** @var FeatureManager $featureManager */
    protected $featureManager;

    public function __construct(FeatureManager $featureManager)
    {
        $this->featureManager = $featureManager;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('feature_enabled', [$this, 'featureEnabled']),
        ];
    }

    public function featureEnabled(string $featureName): bool
    {
        return $this->featureManager->isFeatureEnabled($featureName);
    }

    public function getName(): string
    {
        return 'feature_extension';
    }
}

