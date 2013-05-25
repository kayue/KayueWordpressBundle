<?php

namespace Kayue\WordpressBundle\Model;

interface PostManagerInterface
{
    public function findOnePostById($id);

    public function findOnePostBySlug($slug);
}
