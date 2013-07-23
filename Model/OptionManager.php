<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;

class OptionManager extends AbstractManager implements OptionManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var ArrayCache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->em         = $this->getEntityManager();
        $this->repository = $this->em->getRepository('KayueWordpressBundle:Option');
        $this->cache      = new ArrayCache();
    }

    public function findOneOptionByName($name)
    {
        if (false === $option = $this->cache->fetch($name)) {
            /** @var $option Option */
            $option = $this->repository->findOneBy(array(
                'name' => $name
            ));

            if($option !== null) {
                $this->cacheOption($option);
            }
        }

        return $option;
    }

    private function cacheOption(Option $option)
    {
        $this->cache->save($option->getName(), clone $option);
    }
}
