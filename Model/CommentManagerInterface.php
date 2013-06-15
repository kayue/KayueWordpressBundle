<?php

namespace Kayue\WordpressBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface CommentManagerInterface
{
    public function createComment(PostInterface $post, Request $request);

    public function deleteComment(CommentInterface $comment);

    public function updateComment(CommentInterface $comment);

    public function getClass();

    public function findCommentsByPost(PostInterface $post);
}