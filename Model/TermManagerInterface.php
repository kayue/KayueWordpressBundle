<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\Taxonomy;

interface TermManagerInterface
{
    public function findTermsByPost(Post $post, Taxonomy $taxonomy = null);
}