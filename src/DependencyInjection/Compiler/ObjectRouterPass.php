<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\Router\ObjectRouter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ObjectRouterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ObjectRouter::class)) {
            return;
        }

        $objectRouter = $container->findDefinition(ObjectRouter::class);

        $taggedServices = $container->findTaggedServiceIds('object_router.delegated_router');

        foreach ($taggedServices as $id => $tags) {
            $objectRouter->addMethodCall('addDelegatedRouter', [new Reference($id)]);
        }
    }
}
