<?php

namespace Kayue\WordpressBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ShortcodeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kayue_wordpress.shortcode_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'kayue_wordpress.shortcode_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'kayue_wordpress.shortcode'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addShortcode',
                array(new Reference($id))
            );
        }
    }
}