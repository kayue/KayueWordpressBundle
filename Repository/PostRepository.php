<?php

namespace Kayue\WordpressBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kayue\WordpressBundle\Entity\Post;

class PostRepository extends EntityRepository
{
    public function getQueryBuilder()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from('Kayue\WordpressBundle\Entity\Post', 'p')
        ;
    }

    public function findAttachmentsByPost(Post $post)
    {
        return $this->getQueryBuilder()
            ->andWhere('p.type = :type AND p.parent = :post')
            ->setParameter('type', 'attachment')
            ->setParameter('post', $post)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAttachmentById($id)
    {
        return $this->getQueryBuilder()
            ->where('p.type = :type AND p.id = :id')
            ->setParameter('type', 'attachment')
            ->setParameter('post', $id)
            ->getQuery()
            ->getResult()
        ;
    }
} 
