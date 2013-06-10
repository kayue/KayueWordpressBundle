<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class UserMetaManager implements UserMetaManagerInterface
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
        $this->repository = $em->getRepository('KayueWordpressBundle:UserMeta');
    }

    public function addMeta(User $user, UserMeta $meta)
    {
        $user->addMeta($meta);
    }

    public function findAllMetasByUser(User $user)
    {
        return $user->getMetas();
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
