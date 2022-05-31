<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TrackVoterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(TrackDeciderInterface::class)) {
            return;
        }

        $trackDecider = $container->findDefinition(TrackDeciderInterface::class);

        $taggedServices = $container->findTaggedServiceIds('mass_track_import.voter');

        foreach ($taggedServices as $id => $tags) {
            $trackDecider->addMethodCall('addVoter', [new Reference($id)]);
        }
    }
}
