<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Entity\Post;

interface CategoryManagerInterface
{
    public function findCategoriesByPost(Post $post);
}
