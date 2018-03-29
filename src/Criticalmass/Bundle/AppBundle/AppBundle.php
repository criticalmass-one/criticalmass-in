<?php

namespace Criticalmass\Bundle\AppBundle;

use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\TimelineCollectorPass;
use Criticalmass\Component\Timeline\Collector\TimelineCollectorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerForAutoconfiguration(TimelineCollectorInterface::class)->addTag('timeline.collector');
        $container->addCompilerPass(new TimelineCollectorPass());
    }
}
