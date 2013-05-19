<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Configuration;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Symfony\Component\DependencyInjection\Container;

class BlogManager implements BlogManagerInterface
{
    /**
     * @var Container
     */
    protected $container;

    protected $blogs = array();

    /**
     * @param Container $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param integer $name
     * @return Blog
     */
    public function findBlogById($id)
    {
        if(!isset($this->blogs[$id])) {
            $em = WordpressEntityManager::create(
                $this->container->get('database_connection'),
                $this->container->get(sprintf('doctrine.orm.%s_configuration', 'default'))
            );

            // TODO: Allow other cache driver.
            $em->getMetadataFactory()->setCacheDriver(new ArrayCache());

            $em->setBlogId($id);

            $this->blogs[$id] = new Blog($id, $em);
        }

        return $this->blogs[$id];
    }
}