<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;

class PostMetaManager extends AbstractManager implements PostMetaManagerInterface
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
     * Constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->em = $this->getEntityManager();
        $this->repository = $this->em->getRepository('KayueWordpressBundle:PostMeta');
    }

    public function addMeta(Post $post, PostMeta $meta)
    {
        $post->addMeta($meta);
    }

    public function findAllMetasByPost(Post $post)
    {
        return $post->getMetas();
    }

    public function findMetasBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function findOneMetaBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}
