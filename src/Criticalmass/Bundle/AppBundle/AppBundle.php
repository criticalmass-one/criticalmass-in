<?php

namespace Criticalmass\Bundle\AppBundle;

use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\SocialNetworkPass;
use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\TimelineCollectorPass;
use Criticalmass\Component\SocialNetwork\Network\NetworkInterface;
use Criticalmass\Component\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TimelineCollectorPass());
        $container->addCompilerPass(new SocialNetworkPass());

        $container->registerForAutoconfiguration(NetworkInterface::class)->addTag('social_network.network');
        $container->registerForAutoconfiguration(NetworkFeedFetcherInterface::class)->addTag('social_network.network_fetcher');
    }
}
