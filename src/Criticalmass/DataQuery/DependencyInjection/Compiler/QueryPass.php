<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\DependencyInjection\Compiler;

use App\Criticalmass\DataQuery\Manager\QueryManagerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QueryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(QueryManagerInterface::class)) {
            return;
        }

        $queryManager = $container->findDefinition(QueryManagerInterface::class);

        $taggedServices = $container->findTaggedServiceIds('data_query.query');

        foreach ($taggedServices as $id => $tags) {
            $queryManager->addMethodCall('addQuery', [new Reference($id)]);
        }
    }
}
