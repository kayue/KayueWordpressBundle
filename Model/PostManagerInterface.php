<?php

namespace Kayue\WordpressBundle\Model;

interface PostManagerInterface
{
    public function findPostById($id);

    public function findPostBySlug($slug);
}
