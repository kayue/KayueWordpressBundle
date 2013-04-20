<?php

namespace Kayue\WordpressBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class WordpressFactory extends AbstractFactory
{
    protected $options = array(
        'name' => 'REMEMBERME',
        'lifetime' => 31536000,
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'always_remember_me' => false,
        'remember_me_parameter' => '_remember_me',
    );

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
        $templateId = 'kayue_wordpress.security.authentication.provider';
        $authProviderId = $templateId . '.' . $id;

        $container->setDefinition($authProviderId, new DefinitionDecorator($templateId))
            ->addArgument(new Reference('security.user_checker'))
        ;

        return $authProviderId;
    }

    protected function createListener($container, $id, $config, $userProviderId)
    {
        $templateId = 'kayue_wordpress.security.authentication.rememberme';
        $rememberMeServicesId = $templateId . '.' .$id;

        $rememberMeServices = $container->setDefinition($rememberMeServicesId, new DefinitionDecorator($templateId));
        $rememberMeServices->replaceArgument(2, new Reference($userProviderId));
        // TODO: set $options['name'] to WordPress logged in cookie.
        $rememberMeServices->replaceArgument(3, array_intersect_key($config, $this->options));

        $listenerId = $this->getListenerId();
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('kayue_wordpress.security.authentication.listener'));
        $listener->replaceArgument(1, new Reference($rememberMeServicesId));

        return $listenerId;
    }

    /**
     * Return the id of the listener template.
     *
     * @return string
     */
    protected function getListenerId()
    {
        return 'kayue_wordpress.authentication.listener';
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