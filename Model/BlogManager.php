<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\DBALException;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Kayue\WordpressBundle\Event\SwitchBlogEvent;
use Kayue\WordpressBundle\WordpressEvents;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
     * @var int
     */
    protected $currentBlogId = 1;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  integer                    $id
     * @throws \Doctrine\ORM\ORMException
     * @return Blog
     */
    public function findBlogById($id)
    {
        $config = $this->getEntityManagerConfiguration();

        if (!isset($this->blogs[$id])) {
            $em = WordpressEntityManager::create($this->container->get('database_connection'), $config);

            $em->getMetadataFactory()->setCacheDriver($this->getCacheImpl('metadata_cache', $id));
            $em->getConfiguration()->setQueryCacheImpl($this->getCacheImpl('query_cache', $id));
            $em->getConfiguration()->setResultCacheImpl($this->getCacheImpl('result_cache', $id));

            try {
                if (null === $em->getRepository('KayueWordpressBundle:Blog')->findOneBy(array('id'=>$id))) {
                    throw new \Doctrine\ORM\ORMException(sprintf('Blog %d was not found.', $id));
                }
            } catch(DBALException $e) {
                if($id > 1) {
                    throw new \Exception('Multisite is not installed on your WordPress.');
                }
            }

            $em->setBlogId($id);

            $blog = new Blog();
            $blog->setId($id);
            $blog->setEntityManager($em);

            $this->blogs[$id] = $blog;
        }

        return $this->blogs[$id];
    }

    public function getCurrentBlog()
    {
        return $this->findBlogById($this->getCurrentBlogId());
    }

    public function getCurrentBlogId()
    {
        return $this->currentBlogId;
    }

    public function setCurrentBlogId($currentBlogId)
    {
        $this->currentBlogId = $currentBlogId;

        $event = new SwitchBlogEvent($this->getCurrentBlog());
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(WordpressEvents::SWITCH_BLOG, $event);
    }

    private function getEntityManagerConfiguration()
    {
        $entityManagerName = $this->container->getParameter('kayue_wordpress.entity_manager_name');
        return $this->container->get('doctrine.orm.'.$entityManagerName.'_entity_manager')->getConfiguration();
    }

    /**
     * Loads a configured object manager metadata, query or result cache driver.
     *
     * @param  string           $cacheName
     *
     * @return \Doctrine\Common\Cache\Cache
     *
     * @throws \InvalidArgumentException In case of unknown driver type.
     */
    protected function getCacheImpl($cacheName, $blogId)
    {
        $config = $this->getEntityManagerConfiguration();

        switch ($cacheName) {
            case 'metadata_cache':
                $baseCache = $config->getMetadataCacheImpl();
                break;
            case 'query_cache':
                $baseCache = $config->getQueryCacheImpl();
                break;
            case 'result_cache':
                $baseCache = $config->getResultCacheImpl();
                break;
            default:
                throw new \InvalidArgumentException(sprintf('"%s" is an unrecognized Doctrine cache name.
                        Supported cache names are: "metadata_cache", "query_cache" and "result_cache"', $cacheName));
        }

        $namespace = 'sf2_kayue_wordpress_bundle_blog_'.$blogId.'_'.md5($this->container->getParameter('kernel.root_dir').$this->container->getParameter('kernel.environment'));

        $className = get_class($baseCache);

        switch ($className) {
            case 'Doctrine\Common\Cache\ApcCache':
            case 'Doctrine\Common\Cache\ArrayCache':
            case 'Doctrine\Common\Cache\XcacheCache':
            case 'Doctrine\Common\Cache\WinCacheCache':
            case 'Doctrine\Common\Cache\ZendDataCache':
                $cache = new $className();
                break;
            case 'Doctrine\Common\Cache\MemcacheCache':
                $memcache = $baseCache->getMemcache();
                $cache = new $className();
                $cache->setMemcache($memcache);
                break;
            case 'Doctrine\Common\Cache\MemcachedCache':
                $memcached = $baseCache->getMemcached();
                $cache = new $className();
                $cache->setMemcached($memcached);
                break;
            case 'Doctrine\Common\Cache\RedisCache':
                $redis = $baseCache->getRedis();
                $cache = new $className();
                $cache->setRedis($redis);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown or unsupported cache type class in configuration: "%s"', get_class($baseCache)));
        }
        
        $cache->setNamespace($namespace);

        return $cache;
    }

}
