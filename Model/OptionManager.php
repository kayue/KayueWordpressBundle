<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class OptionManager extends OptionManagerInterface
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
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Option');
        $this->cache      = new ArrayCache();
    }

    public function findAllAutoloadOptions()
    {
        // TODO: Cache it
        return $this->repository->findBy(array(
            'autoload' => 'yes'
        ));
    }

    public function findOptionByName($name)
    {
        return $this->repository->findBy(array(
            'name' => $name
        ));
    }
}
