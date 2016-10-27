<?php

namespace Kayue\WordpressBundle\Repository;

use Kayue\WordpressBundle\Entity\Post;
use Doctrine\ORM\AbstractQuery;

class PostMetaRepository extends AbstractRepository
{
    public function getMetasByPost(Post $post, $key, $hydrationMode = AbstractQuery::HYDRATE_SIMPLEOBJECT)
    {
        return $this->getQueryBuilder()
            ->andWhere('pm.post = :post')
            ->andWhere('pm.key = :key')
            ->setParameter('post', $post)
            ->setParameter('key', $key)
            ->getQuery()
            ->getResult($hydrationMode)
        ;
    }

    public function getMetaByPost(Post $post, $key, $hydrationMode = AbstractQuery::HYDRATE_SIMPLEOBJECT)
    {
        return $this->getQueryBuilder()
            ->andWhere('pm.post = :post')
            ->andWhere('pm.key = :key')
            ->setMaxResults(1)
            ->setParameter('post', $post)
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult($hydrationMode)
        ;
    }

    public function getAlias()
    {
        return 'pm';
    }
}
