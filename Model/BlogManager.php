<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\DBALException;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Kayue\WordpressBundle\Event\SwitchBlogEvent;
use Kayue\WordpressBundle\WordpressEvents;
use Symfony\Component\DependencyInjection\Container;
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
        if (!isset($this->blogs[$id])) {
            $em = WordpressEntityManager::create(
                $this->container->get('database_connection'),
                $this->container->get('doctrine.orm.entity_manager')->getConfiguration()
            );

            // TODO: Set query cache and result cache here
            // $em->getMetadataFactory()->setCacheDriver($this->getCacheImpl('metadata_cache'));
            // $em->getConfiguration()->setQueryCacheImpl($this->getCacheImpl('query_cache'));
            // $em->getConfiguration()->setResultCacheImpl($this->getCacheImpl('result_cache'));

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

    /**
     * Loads a configured object manager metadata, query or result cache driver.
     *
     * @param  string           $cacheName
     *
     * @return \Doctrine\Common\Cache\Cache
     *
     * @throws \InvalidArgumentException In case of unknown driver type.
     */
    protected function getCacheImpl($cacheName)
    {
        $orm = $this->container->getParameter('kayue_wordpress.orm');



        switch ($orm[$name]['type']) {
            case 'memcache':
                $memcacheClass = !empty($cacheDriver['class']) ? $cacheDriver['class'] : '%'.$this->getObjectManagerElementName('cache.memcache.class').'%';
                $memcacheInstanceClass = !empty($cacheDriver['instance_class']) ? $cacheDriver['instance_class'] : '%'.$this->getObjectManagerElementName('cache.memcache_instance.class').'%';
                $memcacheHost = !empty($cacheDriver['host']) ? $cacheDriver['host'] : '%'.$this->getObjectManagerElementName('cache.memcache_host').'%';
                $memcachePort = !empty($cacheDriver['port']) || (isset($cacheDriver['port']) && $cacheDriver['port'] === 0)  ? $cacheDriver['port'] : '%'.$this->getObjectManagerElementName('cache.memcache_port').'%';
                $cacheDef = new Definition($memcacheClass);
                $memcacheInstance = new Definition($memcacheInstanceClass);
                $memcacheInstance->addMethodCall('connect', array(
                    $memcacheHost, $memcachePort
                ));
                $container->setDefinition($this->getObjectManagerElementName(sprintf('%s_memcache_instance', $objectManager['name'])), $memcacheInstance);
                $cacheDef->addMethodCall('setMemcache', array(new Reference($this->getObjectManagerElementName(sprintf('%s_memcache_instance', $objectManager['name'])))));
                break;
            case 'memcached':
                $memcachedClass = !empty($cacheDriver['class']) ? $cacheDriver['class'] : '%'.$this->getObjectManagerElementName('cache.memcached.class').'%';
                $memcachedInstanceClass = !empty($cacheDriver['instance_class']) ? $cacheDriver['instance_class'] : '%'.$this->getObjectManagerElementName('cache.memcached_instance.class').'%';
                $memcachedHost = !empty($cacheDriver['host']) ? $cacheDriver['host'] : '%'.$this->getObjectManagerElementName('cache.memcached_host').'%';
                $memcachedPort = !empty($cacheDriver['port']) ? $cacheDriver['port'] : '%'.$this->getObjectManagerElementName('cache.memcached_port').'%';
                $cacheDef = new Definition($memcachedClass);
                $memcachedInstance = new Definition($memcachedInstanceClass);
                $memcachedInstance->addMethodCall('addServer', array(
                    $memcachedHost, $memcachedPort
                ));
                $container->setDefinition($this->getObjectManagerElementName(sprintf('%s_memcached_instance', $objectManager['name'])), $memcachedInstance);
                $cacheDef->addMethodCall('setMemcached', array(new Reference($this->getObjectManagerElementName(sprintf('%s_memcached_instance', $objectManager['name'])))));
                break;
            case 'redis':
                $redisClass = !empty($cacheDriver['class']) ? $cacheDriver['class'] : '%'.$this->getObjectManagerElementName('cache.redis.class').'%';
                $redisInstanceClass = !empty($cacheDriver['instance_class']) ? $cacheDriver['instance_class'] : '%'.$this->getObjectManagerElementName('cache.redis_instance.class').'%';
                $redisHost = !empty($cacheDriver['host']) ? $cacheDriver['host'] : '%'.$this->getObjectManagerElementName('cache.redis_host').'%';
                $redisPort = !empty($cacheDriver['port']) ? $cacheDriver['port'] : '%'.$this->getObjectManagerElementName('cache.redis_port').'%';
                $cacheDef = new Definition($redisClass);
                $redisInstance = new Definition($redisInstanceClass);
                $redisInstance->addMethodCall('connect', array(
                    $redisHost, $redisPort
                ));
                $container->setDefinition($this->getObjectManagerElementName(sprintf('%s_redis_instance', $objectManager['name'])), $redisInstance);
                $cacheDef->addMethodCall('setRedis', array(new Reference($this->getObjectManagerElementName(sprintf('%s_redis_instance', $objectManager['name'])))));
                break;
            case 'apc':
            case 'array':
            case 'xcache':
            case 'wincache':
            case 'zenddata':
                $cacheDef = new Definition('%'.$this->getObjectManagerElementName(sprintf('cache.%s.class', $cacheDriver['type'])).'%');
                break;
            default:
                throw new \InvalidArgumentException(sprintf('"%s" is an unrecognized Doctrine cache driver.', $cacheDriver['type']));
        }

        $cacheDef->setPublic(false);
        // generate a unique namespace for the given application
        $namespace = 'sf2'.$this->getMappingResourceExtension().'_'.$objectManager['name'].'_'.md5($container->getParameter('kernel.root_dir').$container->getParameter('kernel.environment'));
        $cacheDef->addMethodCall('setNamespace', array($namespace));

        $container->setDefinition($cacheDriverService, $cacheDef);
    }
}
