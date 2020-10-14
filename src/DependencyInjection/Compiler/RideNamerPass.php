<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\RideNamer\RideNamerList;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RideNamerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(RideNamerList::class)) {
            return;
        }

        $rideNamerList = $container->findDefinition(RideNamerList::class);

        $taggedServices = $container->findTaggedServiceIds('ride_namer');

        foreach ($taggedServices as $id => $tags) {
            $rideNamerList->addMethodCall('addRideNamer', [new Reference($id)]);
        }
    }
}
