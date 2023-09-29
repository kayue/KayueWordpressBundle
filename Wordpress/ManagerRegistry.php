<?php

namespace Kayue\WordpressBundle\Wordpress;

use BadMethodCallException;
use Doctrine\Common\Cache\Psr6\CacheAdapter;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ProxyAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
    protected $previousBlogId = 1;
    protected $managers = [];

    private $queryCache;
    private $resultCache;

    public function __construct(
        Connection $connection,
        EntityManager $defaultEntityManager,
        EventDispatcherInterface $eventDispatcher,
        $rootDir,
        $environment,
        AdapterInterface $metadataCache,
        AdapterInterface $queryCache,
        AdapterInterface $resultCache
    ) {
        $this->connection = $connection;
        $this->defaultEntityManager = $defaultEntityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->rootDir = $rootDir;
        $this->environment = $environment;
        $this->metadataCache = $metadataCache;
        $this->queryCache = $queryCache;
        $this->resultCache = $resultCache;
    }

    /**
     * @param int $blogId
     *
     * @return WordpressEntityManager
     */
    public function getManager($blogId = null)
    {
        if ($blogId !== null && $blogId !== $this->currentBlogId) {
            $this->setCurrentBlogId($blogId);
        }

        if (!isset($this->managers[$this->currentBlogId])) {
            $config = Setup::createAnnotationMetadataConfiguration([], 'prod' !== $this->environment, null, null, false);
            $config->setAutoGenerateProxyClasses(true);
            $config->setProxyDir($this->defaultEntityManager->getConfiguration()->getProxyDir());

            $em = WordpressEntityManager::create($this->connection, $config);

            $em->setBlogId($this->currentBlogId);

            $em->getConfiguration()->setMetadataCache($this->getCacheProvider($this->metadataCache, $this->currentBlogId));
            $em->getConfiguration()->setQueryCache($this->getCacheProvider($this->queryCache, $this->currentBlogId));
            $em->getConfiguration()->setResultCache($this->getCacheProvider($this->resultCache, $this->currentBlogId));

            $this->managers[$this->currentBlogId] = $em;
        }

        return $this->managers[$this->currentBlogId];
    }

    /**
     * Switches the active blog until the user calls the restorePreviousBlog() method.
     *
     * @param $blogId
     */
    public function setCurrentBlogId($blogId)
    {
        if ($this->currentBlogId === $blogId) {
            return;
        }

        $this->previousBlogId = $this->currentBlogId;
        $this->currentBlogId = $blogId;
    }

    /**
     * Switches active blog back after user calls the setCurrentBlogId() method.
     */
    public function restorePreviousBlog()
    {
        $this->setCurrentBlogId($this->previousBlogId);
    }

    protected function getCacheProvider(CacheItemPoolInterface $pool, $blogId)
    {
        $cache = DoctrineProvider::wrap($pool);
        $namespace = sprintf('wordpress_blog_%s_', $blogId);
        $cache->setNamespace($namespace);

        return CacheAdapter::wrap($cache);
    }

    public function getDefaultConnectionName()
    {
        throw new BadMethodCallException();
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
        throw new BadMethodCallException();
    }

    public function getDefaultManagerName()
    {
        throw new BadMethodCallException();
    }

    public function getManagers()
    {
        throw new BadMethodCallException();
    }

    public function resetManager($name = null)
    {
        throw new BadMethodCallException();
    }

    public function getAliasNamespace($alias)
    {
        throw new BadMethodCallException();
    }

    public function getManagerNames()
    {
        throw new BadMethodCallException();
    }

    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        throw new BadMethodCallException();
    }

    public function getManagerForClass($class)
    {
        throw new BadMethodCallException();
    }
}
