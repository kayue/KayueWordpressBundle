<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class PostManager implements PostManagerInterface
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
        $this->em         = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Post');
    }

    public function findPostById($id)
    {
        return $this->repository->findBy(array(
            'id' => $id,
        ));
    }

    public function findPostBySlug($slug)
    {
        return $this->repository->findBy(array(
            'slug' => $slug,
        ));
    }
}
