<?php

namespace Kayue\WordpressBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kayue_wordpress');

        $rootNode->children()
            ->scalarNode('logged_in_key')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('logged_in_salt')->isRequired()->cannotBeEmpty()->end()
            /* save for later
            ->scalarNode('cookie_path')->defaultValue(null)->end()
            ->scalarNode('cookie_domain')->defaultValue(null)->end()
            ->scalarNode('table_prefix')->defaultValue('wp_')->end()
            ->scalarNode('main_site')->defaultValue(null)->end()
            ->arrayNode('sites')
                ->prototype('array')
                    ->children()
                        ->scalarNode('pattern')->end()
                        ->scalarNode('hostname_pattern')->defaultValue('')->end()
                        ->scalarNode('entity_manager')->defaultValue(null)->end()
                        ->arrayNode('requirements')->prototype('scalar')->defaultValue(array())->end()
                    ->end()
                ->end()
            ->end()
            */
        ->end();

        return $treeBuilder;
    }
}