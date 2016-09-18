<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Caldera\Bundle\CalderaBundle\CalderaBundle(),
            new Caldera\Bundle\CriticalmassSiteBundle\CalderaCriticalmassSiteBundle(),
            new Caldera\Bundle\CriticalmassCoreBundle\CalderaCriticalmassCoreBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Lsw\MemcacheBundle\LswMemcacheBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
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
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
