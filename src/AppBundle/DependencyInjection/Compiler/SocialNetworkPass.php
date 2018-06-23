<?php

namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\Criticalmass\SocialNetwork\FeedFetcher\FeedFetcher;
use AppBundle\Criticalmass\SocialNetwork\NetworkManager\NetworkManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SocialNetworkPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->processNetworkFeedFetcher($container);
        $this->processNetworkManager($container);
    }

    protected function processNetworkFeedFetcher(ContainerBuilder $container): void
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

    protected function processNetworkManager(ContainerBuilder $container): void
    {
        if (!$container->has(NetworkManager::class)) {
            return;
        }

        $feedFetcher = $container->findDefinition(NetworkManager::class);

        $taggedServices = $container->findTaggedServiceIds('social_network.network');

        foreach ($taggedServices as $id => $tags) {
            $feedFetcher->addMethodCall('addNetwork', [new Reference($id)]);
        }
    }
}
