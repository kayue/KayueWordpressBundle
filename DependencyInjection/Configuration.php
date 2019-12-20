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
            ->scalarNode('site_url')->defaultValue(null)->end()
            ->scalarNode('logged_in_key')->defaultValue(null)->end()
            ->scalarNode('logged_in_salt')->defaultValue(null)->end()
            ->scalarNode('cookie_path')->defaultValue('/')->end()
            ->scalarNode('cookie_domain')->defaultValue(null)->end()
            ->scalarNode('table_prefix')->defaultValue('wp_')->end()
            ->scalarNode('connection')->defaultValue('default')->end()
            ->arrayNode('orm')->addDefaultsIfNotSet()->children()
                ->scalarNode('metadata_cache_pool')->defaultValue('cache.system')->end()
                ->scalarNode('query_cache_pool')->defaultValue('cache.app')->end()
                ->scalarNode('result_cache_pool')->defaultValue('cache.app')->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

}
