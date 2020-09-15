<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\Router\DelegatedRouterManager\DelegatedRouterManagerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ObjectRouterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(DelegatedRouterManagerInterface::class)) {
            return;
        }

        $objectRouter = $container->findDefinition(DelegatedRouterManagerInterface::class);

        $taggedServices = $container->findTaggedServiceIds('object_router.delegated_router');

        foreach ($taggedServices as $id => $tags) {
            $objectRouter->addMethodCall('addDelegatedRouter', [new Reference($id)]);
        }
    }
}
