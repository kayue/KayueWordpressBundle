<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\DBALException;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;

class BlogManager implements BlogManagerInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $blogs = array();

    /**
     * @param Container $container
     * @param LoggerInterface $logger
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
                $this->container->get('doctrine.orm.entity_manager')->getConfiguration()
            );

            // use brand a new cache each entity manager to prevent faulty cache
            $em->getMetadataFactory()->setCacheDriver(new ArrayCache());

            if(null === $em->getRepository('KayueWordpressBundle:Blog')->findOneById($id)) {
                throw new \Doctrine\ORM\ORMException(sprintf('Blog %d was not found.', $id));
            }

            $em->setBlogId($id);

            $this->blogs[$id] = new Blog($id, $em);
        }

        return $this->blogs[$id];
    }
}