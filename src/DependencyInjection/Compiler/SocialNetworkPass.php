<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SocialNetworkPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->processNetworkFetcher($container);
    }

    protected function processNetworkFetcher(ContainerBuilder $container): void
    {
        if (!$container->has(NetworkManagerInterface::class)) {
            return;
        }

        $feedFetcher = $container->findDefinition(NetworkManagerInterface::class);

        $taggedServices = $container->findTaggedServiceIds('social_network.network');

        foreach ($taggedServices as $id => $tags) {
            $feedFetcher->addMethodCall('addNetwork', [new Reference($id)]);
        }
    }
}
