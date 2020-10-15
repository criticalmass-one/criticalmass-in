<?php declare(strict_types=1);

namespace App;

use App\Criticalmass\DataQuery\DependencyInjection\Compiler\ParameterPass;
use App\Criticalmass\DataQuery\DependencyInjection\Compiler\QueryPass;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Criticalmass\Profile\Deletion\UserPartDeleter\UserPartDeleterInterface;
use App\Criticalmass\RideNamer\RideNamerInterface;
use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\Criticalmass\Sharing\Network\ShareNetworkInterface;
use App\Criticalmass\SocialNetwork\DependencyInjection\Compiler\SocialNetworkFetcherPass;
use App\Criticalmass\SocialNetwork\DependencyInjection\Compiler\SocialNetworkPass;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
use App\DependencyInjection\Compiler\ObjectRouterPass;
use App\DependencyInjection\Compiler\RideNamerPass;
use App\DependencyInjection\Compiler\ShareNetworkPass;
use App\DependencyInjection\Compiler\TimelineCollectorPass;
use App\DependencyInjection\Compiler\TrackVoterPass;
use App\DependencyInjection\Compiler\TwigSeoExtensionPass;
use App\DependencyInjection\Compiler\UserPartDeleterCollectorPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

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

    public function registerBundles()
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

        $container->addCompilerPass(new ShareNetworkPass());
        $container->registerForAutoconfiguration(ShareNetworkInterface::class)->addTag('share.network');

        $container->addCompilerPass(new RideNamerPass());
        $container->registerForAutoconfiguration(RideNamerInterface::class)->addTag('ride_namer');

        $container->addCompilerPass(new TwigSeoExtensionPass());

        $container->addCompilerPass(new TrackVoterPass());
        $container->registerForAutoconfiguration(VoterInterface::class)->addTag('mass_track_import.voter');

        $container->addCompilerPass(new QueryPass());
        $container->registerForAutoconfiguration(QueryInterface::class)->addTag('data_query.query');

        $container->addCompilerPass(new ParameterPass());
        $container->registerForAutoconfiguration(ParameterInterface::class)->addTag('data_query.parameter');

        $container->addCompilerPass(new SocialNetworkPass());
        $container->registerForAutoconfiguration(NetworkInterface::class)->addTag('social_network.network');

        $container->addCompilerPass(new UserPartDeleterCollectorPass());
        $container->registerForAutoconfiguration(UserPartDeleterInterface::class)->addTag('user.part_deleter');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
}
