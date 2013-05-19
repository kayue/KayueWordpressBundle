<?php

namespace Kayue\WordpressBundle;

use Doctrine\DBAL\Types\Type;
use Kayue\WordpressBundle\DependencyInjection\Security\Factory\WordpressFactory;
use Kayue\WordpressBundle\Types\WordpressIdType;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kayue\WordpressBundle\Types\WordpressMetaType;

class KayueWordpressBundle extends Bundle
{

    public function boot()
    {
        parent::boot();

        if (!Type::hasType(WordpressMetaType::NAME)) {
            Type::addType(WordpressMetaType::NAME, 'Kayue\WordpressBundle\Types\WordpressMetaType');
        }

        if (!Type::hasType(WordpressIdType::NAME)) {
            Type::addType(WordpressIdType::NAME, 'Kayue\WordpressBundle\Types\WordpressIdType');
        }
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WordpressFactory());
    }
}
