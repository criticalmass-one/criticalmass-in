<?php

namespace Caldera\GeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('caldera_geo');

        $rootNode
            ->children()
            ->scalarNode('track_class')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()

            ->scalarNode('position_class')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
