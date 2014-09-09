<?php

namespace Kayue\WordpressBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds services tagged "kayue_wordpress.extra_transformer" to the transformer registry
 */
class ExtraTransformerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kayue_wordpress.extra_transformer.registry')) {
            return;
        }

        $definition = $container->getDefinition(
            'kayue_wordpress.extra_transformer.registry'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'kayue_wordpress.extra_transformer'
        );

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addTransformer',
                    array($attributes['alias'], new Reference($id))
                );
            }
        }
    }
}
