<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\Sharing\SocialSharer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ShareNetworkPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(SocialSharer::class)) {
            return;
        }

        $socialSharer = $container->findDefinition(SocialSharer::class);

        $taggedServices = $container->findTaggedServiceIds('share.network');

        foreach ($taggedServices as $id => $tags) {
            $socialSharer->addMethodCall('addShareNetwork', [new Reference($id)]);
        }
    }
}
