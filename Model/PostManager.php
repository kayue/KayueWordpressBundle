<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;

class PostManager extends AbstractManager implements PostManagerInterface
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
     * @param EntityManager $em
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->em         = $this->getEntityManager();
        $this->repository = $this->em->getRepository('KayueWordpressBundle:Post');
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
