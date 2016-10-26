<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Caldera\Bundle\CalderaBundle\CalderaBundle(),
            new Caldera\Bundle\CriticalmassSiteBundle\CalderaCriticalmassSiteBundle(),
            new Caldera\Bundle\CriticalmassCoreBundle\CalderaCriticalmassCoreBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Lsw\MemcacheBundle\LswMemcacheBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Craue\FormFlowBundle\CraueFormFlowBundle(),
            new Caldera\Bundle\CriticalmassRocksBundle\CalderaCriticalmassRocksBundle(),
            new Caldera\Bundle\CriticalmassPhotoBundle\CalderaCriticalmassPhotoBundle(),
            new Caldera\Bundle\CriticalmassBlogBundle\CalderaCriticalmassBlogBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Caldera\Bundle\CriticalmassLiveBundle\CalderaCriticalmassLiveBundle(),
            new Caldera\Bundle\CriticalmassRestBundle\CalderaCriticalmassRestBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Caldera\Bundle\CyclewaysBundle\CalderaCyclewaysBundle(),
            new Caldera\Bundle\CriticalmassTipsBundle\CalderaCriticalmassTipsBundle()
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
