<?php

namespace Kayue\WordpressBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class PreventDoctrineMetadataCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $chainDriverDef = $container->getDefinition('doctrine.orm.default_metadata_driver');

        $calls = $chainDriverDef->getMethodCalls();

        foreach ($calls as $key => $call) {
            if ('addDriver' === $call[0] && 'Kayue\WordpressBundle\Entity' === $call[1][1]) {
                unset($calls[$key]);
                break;
            }
        }

        $chainDriverDef->setMethodCalls($calls);
    }
}
