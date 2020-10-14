<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Twig\Extension\SeoTwigExtension;
use Sonata\SeoBundle\Twig\Extension\SeoExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TwigSeoExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->hasDefinition(SeoExtension::class)) {
            return;
        }

        $definition = $container->getDefinition(SeoExtension::class);
        $definition->setClass(SeoTwigExtension::class);
    }
}
