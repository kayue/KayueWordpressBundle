<?php

namespace Kayue\WordpressBundle;

use Doctrine\DBAL\Types\Type;
use Kayue\WordpressBundle\DependencyInjection\Security\Factory\WordpressFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kayue\WordpressBundle\Types\WordPressMetaType;

class KayueWordpressBundle extends Bundle
{

    public function boot()
    {
        parent::boot();

        if (!Type::hasType(WordPressMetaType::NAME)) {
            Type::addType(WordPressMetaType::NAME, 'Kayue\WordpressBundle\Types\WordPressMetaType');
        }
    }


    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WordpressFactory());
    }
}
