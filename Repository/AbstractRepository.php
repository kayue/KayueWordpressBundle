<?php

namespace Kayue\WordpressBundle\Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository implements RepositoryInterface
{
    public function getQueryBuilder()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select($this->getAlias())
            ->from($this->getEntityName(), $this->getAlias())
        ;
    }
}
