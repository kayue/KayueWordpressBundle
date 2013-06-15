<?php

namespace Kayue\WordpressBundle\Model;

interface CommentManagerInterface
{
    public function createComment();

    public function deleteComment(CommentInterface $comment);

    public function updateComment(CommentInterface $comment);

    public function getClass();

    public function findCommentsByPost(PostInterface $post);
}