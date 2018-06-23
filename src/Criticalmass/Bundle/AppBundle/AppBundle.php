<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle;

use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\SocialNetworkPass;
use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\TimelineCollectorPass;
use Criticalmass\Bundle\AppBundle\Criticalmass\SocialNetwork\Network\NetworkInterface;
use Criticalmass\Bundle\AppBundle\Criticalmass\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\FeaturePass;
use Criticalmass\Bundle\AppBundle\Feature\FeatureInterface;
use Criticalmass\Bundle\AppBundle\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
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
    }

    public function getParent(): string
    {
        return 'FOSUserBundle';
    }
}
