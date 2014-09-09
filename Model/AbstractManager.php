<?php

namespace Kayue\WordpressBundle\Model;

use Symfony\Component\DependencyInjection\Container;

abstract class AbstractManager
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        $entityManagerName = $this->container->getParameter('kayue_wordpress.entity_manager');

        return $this->container->get('doctrine.orm.'.$entityManagerName.'_entity_manager');
    }
}
