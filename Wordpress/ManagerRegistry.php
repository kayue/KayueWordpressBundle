<?php

namespace Kayue\WordpressBundle\Wordpress;

use BadMethodCallException;
use Doctrine\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ProxyAdapter;
use Symfony\Component\Cache\DoctrineProvider;
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
            // $config->addEntityNamespace('KayueWordpressBundle', 'Kayue\WordpressBundle\Entity');
            $config->setAutoGenerateProxyClasses(true);
            $config->setProxyDir($this->defaultEntityManager->getConfiguration()->getProxyDir());

            $em = WordpressEntityManager::create($this->connection, $config);

            $em->setBlogId($this->currentBlogId);

            // $em->getMetadataFactory()->setCacheDriver($this->getCacheProvider($this->metadataCache, $this->currentBlogId));
            $em->getConfiguration()->setQueryCacheImpl($this->getCacheProvider($this->queryCache, $this->currentBlogId));
            $em->getConfiguration()->setResultCacheImpl($this->getCacheProvider($this->resultCache, $this->currentBlogId));

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
        $namespace = sprintf('wordpress_blog_%s_', $blogId);
        $proxyAdapter = new ProxyAdapter($pool, $namespace);
        $doctrineCache = new DoctrineProvider($proxyAdapter);

        return $doctrineCache;
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
