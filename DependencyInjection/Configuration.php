<?php

namespace Kayue\WordpressBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
            ->scalarNode('site_url')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('logged_in_key')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('logged_in_salt')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('cookie_path')->defaultValue('/')->end()
            ->scalarNode('cookie_domain')->defaultValue(null)->end()
            ->scalarNode('table_prefix')->defaultValue('wp_')->end()
            ->arrayNode('orm')
                ->append($this->getCacheDriverNode('query_cache_driver'))
                ->append($this->getCacheDriverNode('metadata_cache_driver'))
                ->append($this->getCacheDriverNode('result_cache_driver'))
            ->end()
        ->end();

        return $treeBuilder;
    }


    /**
     * Return a ORM cache driver node for an given entity manager
     *
     * @param string $name
     *
     * @return ArrayNodeDefinition
     */
    private function getCacheDriverNode($name)
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($name);

        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifString()
                ->then(function($v) { return array('type' => $v); })
            ->end()
            ->children()
                ->scalarNode('type')->defaultValue('array')->end()
                ->scalarNode('host')->end()
                ->scalarNode('port')->end()
                ->scalarNode('instance_class')->end()
                ->scalarNode('class')->end()
                ->scalarNode('id')->end()
            ->end()
        ;

        return $node;
    }
}
