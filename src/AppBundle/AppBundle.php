<?php declare(strict_types=1);

namespace AppBundle;

use AppBundle\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use AppBundle\DependencyInjection\Compiler\ObjectRouterPass;
use AppBundle\DependencyInjection\Compiler\SocialNetworkPass;
use AppBundle\DependencyInjection\Compiler\TimelineCollectorPass;
use AppBundle\Criticalmass\SocialNetwork\Network\NetworkInterface;
use AppBundle\Criticalmass\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use AppBundle\DependencyInjection\Compiler\FeaturePass;
use AppBundle\Feature\FeatureInterface;
use AppBundle\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(TimelineCollectorInterface::class)->addTag('timeline.collector');
        $container->addCompilerPass(new TimelineCollectorPass());
        
        $container->registerForAutoconfiguration(NetworkInterface::class)->addTag('social_network.network');
        $container->registerForAutoconfiguration(NetworkFeedFetcherInterface::class)->addTag('social_network.network_fetcher');
        $container->addCompilerPass(new SocialNetworkPass());

        $container->addCompilerPass(new FeaturePass());
        $container->registerForAutoconfiguration(FeatureInterface::class)->addTag('feature');

        $container->registerForAutoconfiguration(DelegatedRouterInterface::class)->addTag('object_router.delegated_router');
        $container->addCompilerPass(new ObjectRouterPass());
    }

    public function getParent(): string
    {
        return 'FOSUserBundle';
    }
}
