<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Entity\Post;

interface TagManagerInterface
{
    public function findTagsByPost(Post $post);
}
