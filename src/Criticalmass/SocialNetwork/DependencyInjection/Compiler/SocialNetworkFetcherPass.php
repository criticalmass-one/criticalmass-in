<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\DependencyInjection\Compiler;

use App\Criticalmass\SocialNetwork\FeedFetcher\FeedFetcherInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SocialNetworkFetcherPass implements CompilerPassInterface
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

        $taggedServices = $container->findTaggedServiceIds('social_network.network_feed_fetcher');

        foreach ($taggedServices as $id => $tags) {
            $feedFetcher->addMethodCall('addNetworkFeedFetcher', [new Reference($id)]);
        }
    }
}
