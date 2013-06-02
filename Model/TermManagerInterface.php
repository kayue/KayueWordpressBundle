<?php

namespace Kayue\WordpressBundle\Model;

interface TermManagerInterface
{
    public function findTermsByPost(Post $post, Taxonomy $taxonomy = null);
}