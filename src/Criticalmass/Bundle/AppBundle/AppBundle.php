<?php

namespace Criticalmass\Bundle\AppBundle;

use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\TimelineCollectorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TimelineCollectorPass());
    }
}
