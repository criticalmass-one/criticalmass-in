<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FeatureTwigExtension extends \Twig_Extension
{
    /** @var ContainerInterface $container */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('feature_enabled', [$this, 'featureEnabled']),
        ];
    }

    public function featureEnabled(string $featureName): bool
    {
        $parameterName = sprintf('feature.%s', $featureName);

        return $this->container->getParameter($parameterName) === true;
    }

    public function getName(): string
    {
        return 'feature_extension';
    }
}

