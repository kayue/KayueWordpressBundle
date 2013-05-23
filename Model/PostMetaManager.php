<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\PostMeta;

class PostMetaManager implements PostMetaManagerInterface
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
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Post');
    }

    public function addMeta(Post $post, PostMeta $meta)
    {
        $post->addMeta($meta);
    }

    public function saveMeta(PostMeta $meta)
    {
        // do nothing
    }

    public function deleteMeta(PostMeta $meta)
    {
        $em->delete($meta);
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
