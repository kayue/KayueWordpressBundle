<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\PostMeta;

interface PostMetaManagerInterface
{
    public function addMeta(Post $post, PostMeta $meta);

    public function findAllMetasByPost(Post $post);

    public function findMetasBy(array $criteria);

    public function findOneMetaBy(array $criteria);
}
