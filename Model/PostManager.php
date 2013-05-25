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

    public function findOnePostById($id)
    {
        return $this->repository->findOneBy(array(
            'id' => $id,
        ));
    }

    public function findOnePostBySlug($slug)
    {
        return $this->repository->findOneBy(array(
            'slug' => $slug,
        ));
    }
}
