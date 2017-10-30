<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TimelineCollectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('caldera.criticalmass.timeline') || !$container->has('caldera.criticalmass.timeline.cached')) {
            return;
        }

        $timeline = $container->findDefinition('caldera.criticalmass.timeline');
        $cachedTimeline = $container->findDefinition('caldera.criticalmass.timeline.cached');

        $taggedServices = $container->findTaggedServiceIds('timeline.collector');

        foreach ($taggedServices as $id => $tags) {
            $timeline->addMethodCall('addCollector', [new Reference($id)]);
            $cachedTimeline->addMethodCall('addCollector', [new Reference($id)]);
        }
    }
}