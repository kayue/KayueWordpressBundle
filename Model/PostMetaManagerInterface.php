<?php

namespace Kayue\WordpressBundle\Model;

interface PostMetaManagerInterface
{
    public function addMeta(Post $post, PostMeta $meta);

    public function findAllMetasByPost(Post $post);

    public function findMetasBy(array $criteria);

    public function findOneMetaBy(array $criteria);
}
