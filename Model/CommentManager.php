<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\ORM\EntityManager;

class CommentManager implements CommentManagerInterface
{
    protected $em;
    protected $repository;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository('KayueWordpressBundle:Comment');
    }

    public function createComment()
    {
        $class = $this->getClass();
        $user = new $class;

        return $user;
    }

    public function deleteComment(CommentInterface $comment, $andFlush = true)
    {
        $this->em->remove($comment);

        if($andFlush) {
            $this->em->flush();
        }
    }

    public function updateComment(CommentInterface $comment, $andFlush = true)
    {
        $this->em->persist($comment);

        if($andFlush) {
            $this->em->flush();
        }
    }

    public function getClass()
    {
        return '\Kayue\WordpressBundle\Model\Comment';
    }

    public function findCommentsByPost(PostInterface $post)
    {
        $this->repository->findBy(array(
            'post' => $post
        ));
    }
}