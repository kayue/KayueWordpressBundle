<?php

namespace Kayue\WordpressBundle\Doctrine;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ManagerRegistry
{
    protected $container;
    protected $doctrine;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->doctrine = $container->get('doctrine');
    }

    public function getManager($name = null)
    {
        if(null === $name) {
            $name = $this->getCurrentManagerName();
        }

        return $this->doctrine->getManager($name);
    }

    public function getRepository($persistentObjectName, $persistentManagerName = null)
    {
        return $this->doctrine->getManager($persistentManagerName)->getRepository($persistentObjectName);
    }

    protected function getCurrentManagerName()
    {
        $request = $this->get('request');

        return '';
    }
}