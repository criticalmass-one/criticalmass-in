<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\Profile\Deletion\UserDeleterInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class UserPartDeleterCollectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(UserDeleterInterface::class)) {
            return;
        }

        $userDeleter = $container->findDefinition(UserDeleterInterface::class);

        $taggedServices = $container->findTaggedServiceIds('user.part_deleter');

        foreach ($taggedServices as $id => $tags) {
            $userDeleter->addMethodCall('addPartDeleter', [new Reference($id)]);
        }
    }
}
