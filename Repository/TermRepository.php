<?php

namespace Kayue\WordpressBundle\Repository;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\Taxonomy;
use Doctrine\ORM\AbstractQuery;

class TermRepository extends AbstractRepository
{
    public function findByPost(Post $post, $taxonomy = null, $hydrationMode = AbstractQuery::HYDRATE_SIMPLEOBJECT)
    {
        $queryBuilder = $this->getQueryBuilder()
            ->join('t.taxonomy', 'taxonomy')
            ->join('taxonomy.posts', 'post')
            ->andWhere('post.id = :postId')
            ->setParameter('postId', $post->getId())
        ;

        if (null !== $taxonomy) {
            $queryBuilder
                ->andWhere('taxonomy.name = :taxonomyName')
                ->setParameter('taxonomyName', is_string($taxonomy) ? $taxonomy : $taxonomy->getName())
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getResult($hydrationMode)
        ;
    }

    public function getAlias()
    {
        return 't';
    }
}
