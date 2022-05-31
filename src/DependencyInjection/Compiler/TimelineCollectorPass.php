<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\Timeline\CachedTimeline;
use App\Criticalmass\Timeline\Timeline;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TimelineCollectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(Timeline::class) || !$container->has(CachedTimeline::class)) {
            return;
        }

        $timeline = $container->findDefinition(Timeline::class);
        $cachedTimeline = $container->findDefinition(CachedTimeline::class);

        $taggedServices = $container->findTaggedServiceIds('timeline.collector');

        foreach ($taggedServices as $id => $tags) {
            $timeline->addMethodCall('addCollector', [new Reference($id)]);
            $cachedTimeline->addMethodCall('addCollector', [new Reference($id)]);
        }
    }
}
