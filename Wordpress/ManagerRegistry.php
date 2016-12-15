<?php

namespace Kayue\WordpressBundle\Wordpress;

use Doctrine\Common\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Redis;
use Memcache;
use Memcached;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Kayue\WordpressBundle\WordpressEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ManagerRegistry implements ManagerRegistryInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var EntityManager
     */
    protected $defaultEntityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    protected $rootDir;
    protected $environment;
    protected $currentBlogId = 1;
    protected $entityManagers = [];

    public function __construct(
        Connection $connection,
        EntityManager $defaultEntityManager,
        EventDispatcherInterface $eventDispatcher,
        $rootDir,
        $environment
    )
    {
        $this->connection = $connection;
        $this->defaultEntityManager = $defaultEntityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->rootDir = $rootDir;
        $this->environment = $environment;
    }

    /**
     * @param int $blogId
     *
     * @return WordpressEntityManager
     */
    public function getManager($blogId = null)
    {
        if ($blogId !== null && $blogId !== $this->currentBlogId) {
            $this->currentBlogId = $blogId;
        }

        if (!isset($this->entityManagers[$this->currentBlogId])) {
            $config = Setup::createAnnotationMetadataConfiguration([], 'prod' !== $this->environment, null, null, false);
            $config->addEntityNamespace('KayueWordpressBundle', 'Kayue\WordpressBundle\Entity');
            $config->setAutoGenerateProxyClasses(true);
            $config->setProxyDir($this->defaultEntityManager->getConfiguration()->getProxyDir());

            $em = WordpressEntityManager::create($this->connection, $config);

            $this->eventDispatcher->dispatch(WordpressEvents::CREATE_ENTITY_MANAGER, new GenericEvent($em));

            $em->setBlogId($this->currentBlogId);
            $em->getMetadataFactory()->setCacheDriver($this->getCacheImpl('metadata_cache', $this->currentBlogId));
            $em->getConfiguration()->setQueryCacheImpl($this->getCacheImpl('query_cache', $this->currentBlogId));
            $em->getConfiguration()->setResultCacheImpl($this->getCacheImpl('result_cache', $this->currentBlogId));

            $this->entityManagers[$this->currentBlogId] = $em;
        }

        return $this->entityManagers[$this->currentBlogId];
    }

    /**
     * @param $blogId
     */
    public function setCurrentBlogId($blogId)
    {
        $this->currentBlogId = $blogId;
    }

    /**
     * Loads a configured object manager metadata, query or result cache driver.
     *
     * @param string $cacheName
     *
     * @param $blogId
     * @throws \InvalidArgumentException    In case of unknown driver type.
     * @return \Doctrine\Common\Cache\Cache
     */
    protected function getCacheImpl($cacheName, $blogId)
    {
        $config = $this->defaultEntityManager->getConfiguration();

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

        $namespace = 'sf2_kayue_wordpress_bundle_blog_'.$blogId.'_'.md5($this->rootDir.$this->environment);

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
                $rawStats = $memcache->getExtendedStats();
                $servers = array_keys($rawStats);

                $cache = new $className();
                $newMemcache = new Memcache();

                foreach ($servers as $server) {
                    $host = substr($server, 0, strpos($server, ':'));
                    $port = substr($server, strpos($server, ':') + 1);
                    $newMemcache->connect($host, $port);
                }

                $cache->setMemcache($newMemcache);
                break;
            case 'Doctrine\Common\Cache\MemcachedCache':
                $memcached = $baseCache->getMemcached();
                $servers = $memcached->getServerList();

                $cache = new $className();
                $newMemcached = new Memcached();

                foreach ($servers as $server) {
                    $newMemcached->connect($server['host'], $server['port']);
                }

                $cache->setMemcached($newMemcached);
                break;
            case 'Doctrine\Common\Cache\RedisCache':
                $redis = $baseCache->getRedis();
                $host = $redis->getHost();
                $port = $redis->getPort();

                $cache = new $className();

                $newRedis = new Redis();
                $newRedis->connect($host, $port);

                $cache->setRedis($newRedis);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown or unsupported cache type class in configuration: "%s"', get_class($baseCache)));
        }

        $cache->setNamespace($namespace);

        return $cache;
    }

    public function getDefaultConnectionName()
    {
        // TODO: Implement getDefaultConnectionName() method.
    }

    public function getConnection($name = null)
    {
        return $this->connection;
    }

    public function getConnections()
    {
        return [$this->connection];
    }

    public function getConnectionNames()
    {
        // TODO: Implement getConnectionNames() method.
    }

    public function getDefaultManagerName()
    {
        // TODO: Implement getDefaultManagerName() method.
    }

    public function getManagers()
    {
        // TODO: Implement getManagers() method.
    }

    public function resetManager($name = null)
    {
        // TODO: Implement resetManager() method.
    }

    public function getAliasNamespace($alias)
    {
        // TODO: Implement getAliasNamespace() method.
    }

    public function getManagerNames()
    {
        // TODO: Implement getManagerNames() method.
    }

    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        // TODO: Implement getRepository() method.
    }

    public function getManagerForClass($class)
    {
        // TODO: Implement getManagerForClass() method.
    }
}
