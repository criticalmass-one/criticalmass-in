<?php declare(strict_types=1);

namespace App;

use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Criticalmass\RideNamer\RideNamerInterface;
use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
use App\DependencyInjection\Compiler\ObjectRouterPass;
use App\DependencyInjection\Compiler\RideNamerPass;
use App\DependencyInjection\Compiler\SocialNetworkPass;
use App\DependencyInjection\Compiler\TimelineCollectorPass;
use App\DependencyInjection\Compiler\TrackVoterPass;
use App\DependencyInjection\Compiler\TwigSeoExtensionPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(TimelineCollectorInterface::class)->addTag('timeline.collector');
        $container->addCompilerPass(new TimelineCollectorPass());

        $container->registerForAutoconfiguration(NetworkInterface::class)->addTag('social_network.network');
        $container->addCompilerPass(new SocialNetworkPass());

        $container->registerForAutoconfiguration(DelegatedRouterInterface::class)->addTag('object_router.delegated_router');
        $container->addCompilerPass(new ObjectRouterPass());

        $container->registerForAutoconfiguration(RideNamerInterface::class)->addTag('ride_namer');
        $container->addCompilerPass(new RideNamerPass());

        $container->addCompilerPass(new TwigSeoExtensionPass());

        $container->registerForAutoconfiguration(VoterInterface::class)->addTag('mass_track_import.voter');
        $container->addCompilerPass(new TrackVoterPass());
    }
}
