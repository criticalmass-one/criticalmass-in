<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle;

use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\FeaturePass;
use Criticalmass\Bundle\AppBundle\DependencyInjection\Compiler\TimelineCollectorPass;
use Criticalmass\Bundle\AppBundle\Feature\FeatureInterface;
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

        $container->registerForAutoconfiguration(FeatureInterface::class)->addTag('feature');
        $container->addCompilerPass(new FeaturePass());
    }
}
