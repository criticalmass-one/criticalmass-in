<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\Feature\FeatureManager\FeatureManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class FeaturePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FeatureManager::class)) {
            return;
        }

        $featureManager = $container->findDefinition(FeatureManager::class);

        $taggedServices = $container->findTaggedServiceIds('feature');

        foreach ($taggedServices as $id => $tags) {
            $featureManager->addMethodCall('addFeature', [new Reference($id)]);
        }
    }
}
