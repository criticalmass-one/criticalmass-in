<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\DependencyInjection\Compiler;

use App\Criticalmass\SocialNetwork\FeedFetcher\FeedFetcherInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SocialNetworkPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->processNetworkFetcher($container);
    }

    protected function processNetworkFetcher(ContainerBuilder $container): void
    {
        if (!$container->has(FeedFetcherInterface::class)) {
            return;
        }

        $feedFetcher = $container->findDefinition(FeedFetcherInterface::class);

        $taggedServices = $container->findTaggedServiceIds('social_network.network');

        foreach ($taggedServices as $id => $tags) {
            //$feedFetcher->addMethodCall('addFetchableNetwork', [new Reference($id)]);
        }
    }
}
