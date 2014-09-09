<?php

namespace Kayue\WordpressBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KayueWordpressExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter("kayue_wordpress.".$key, $value);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->loadExtraTransformers($container, $config['extra_transformers']);
    }

    /**
     * Enable extra transformer using the configuration
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function loadExtraTransformers(ContainerBuilder $container, $config)
    {
        $registry = $container->getDefinition(
            'kayue_wordpress.extra_transformer.registry'
        );
        $transformers = $container->findTaggedServiceIds(
            'kayue_wordpress.extra_transformer'
        );

        foreach ($transformers as $id => $attributes) {
            $alias = $attributes[0]['alias'];

            if (array_key_exists($alias, $config)) {
                if ($config[$alias] === false) {
                    $registry->addMethodCall('disable', array($alias));
                } else if (is_array($config[$alias])) {
                    $container->getDefinition($id)->addMethodCall('setOptions', array($config[$alias]));
                }
            }
        }
    }
}
