<?php

namespace Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler;

use Criticalmass\Component\SocialNetwork\FeedFetcher\FeedFetcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SocialNetworkPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FeedFetcher::class)) {
            return;
        }

        $feedFetcher = $container->findDefinition(FeedFetcher::class);

        $taggedServices = $container->findTaggedServiceIds('social_network.network_fetcher');

        foreach ($taggedServices as $id => $tags) {
            $feedFetcher->addMethodCall('addNetworkFeedFetcher', [new Reference($id)]);
        }
    }
}
