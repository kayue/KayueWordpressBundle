<?php

namespace Kayue\WordpressBundle\Repository;

use Kayue\WordpressBundle\Entity\Post;
use Doctrine\ORM\AbstractQuery;

class PostMetaRepository extends AbstractRepository
{
    public function getMetasByPost(Post $post, $key, $hydrationMode = AbstractQuery::HYDRATE_SIMPLEOBJECT)
    {
        return $this->getQueryBuilder()
            ->join('pm.post', 'post')
            ->andWhere('post.id = :postId')
            ->andWhere('pm.key = :key')
            ->setParameter('postId', $post->getId())
            ->setParameter('key', $key)
            ->getQuery()
            ->getResult($hydrationMode)
        ;
    }

    public function getMetaByPost(Post $post, $key, $hydrationMode = AbstractQuery::HYDRATE_SIMPLEOBJECT)
    {
        return $this->getQueryBuilder()
            ->join('pm.post', 'post')
            ->andWhere('post.id = :postId')
            ->andWhere('pm.key = :key')
            ->setMaxResults(1)
            ->setParameter('postId', $post->getId())
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
