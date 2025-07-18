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
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        $container->registerForAutoconfiguration(TimelineCollectorInterface::class)->addTag('timeline.collector');
        $container->addCompilerPass(new TimelineCollectorPass());

        $container->registerForAutoconfiguration(NetworkInterface::class)->addTag('social_network.network');
        $container->addCompilerPass(new SocialNetworkPass());

        $container->registerForAutoconfiguration(DelegatedRouterInterface::class)->addTag('object_router.delegated_router');
        $container->addCompilerPass(new ObjectRouterPass());

        $container->addCompilerPass(new RideNamerPass());
        $container->registerForAutoconfiguration(RideNamerInterface::class)->addTag('ride_namer');

        $container->addCompilerPass(new TwigSeoExtensionPass());

        $container->addCompilerPass(new TrackVoterPass());
        $container->registerForAutoconfiguration(VoterInterface::class)->addTag('mass_track_import.voter');

        $container->addCompilerPass(new SocialNetworkPass());
        $container->registerForAutoconfiguration(NetworkInterface::class)->addTag('social_network.network');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } else {
            $routes->import('../config/{routes}.php');
        }
    }
}
