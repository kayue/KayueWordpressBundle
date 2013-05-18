<?php

namespace Kayue\WordpressBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
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

        $this->loadEntityManager($config['sites'], $container);
    }

    public function loadEntityManager($sites, ContainerBuilder $container)
    {
        foreach($sites as $name => $config) {
            $container
                ->setDefinition(sprintf('kayue_wordpress.orm.%s_entity_manager', $name), new DefinitionDecorator('kayue_wordpress.orm.entity_manager.abstract'))
                ->addMethodCall('setBlogId', array($config['blog_id']))
                ->setArguments(array(
                    new Reference($config['connection']),
                    new Reference($config['configuration'])
                ))
            ;
        }
    }
}
