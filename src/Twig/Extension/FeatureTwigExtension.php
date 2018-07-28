<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Feature\FeatureManager\FeatureManagerInterface;

class FeatureTwigExtension extends \Twig_Extension
{
    /** @var FeatureManagerInterface $featureManager */
    protected $featureManager;

    public function __construct(FeatureManagerInterface $featureManager)
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

