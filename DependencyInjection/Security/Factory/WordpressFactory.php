<?php

namespace Kayue\WordpressBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class WordpressFactory extends AbstractFactory
{
    /**
     * Return the id of a service which implements the AuthenticationProviderInterface.
     *
     * @param ContainerBuilder $container
     * @param string $id             The unique id of the firewall
     * @param array $config         The options array for this listener
     * @param string $userProviderId The id of the user provider
     *
     * @return string never null, the id of the authentication provider
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $authProviderId = 'kayue_wordpress.auth.'.$id;

        $container
            ->setDefinition($authProviderId, new DefinitionDecorator('kayue_wordpress.security.authentication.provider'))
            ->replaceArgument(2, new Reference($userProviderId))
            ->replaceArgument(3, new Reference('security.user_checker'))
        ;

        return $authProviderId;
    }

    /**
     * Return the id of the listener template.
     *
     * @return string
     */
    protected function getListenerId()
    {
        return 'kayue_wordpress.security.authentication.listener';
    }

    public function getPosition()
    {
        return 'remember_me';
    }

    public function getKey()
    {
        return 'kayue_wordpress';
    }
}