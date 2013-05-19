<?php

namespace Kayue\WordpressBundle\Model;

interface BlogManagerInterface
{
    /**
     * @param  integer $name
     * @return Blog
     */
    public function findBlogById($id);
}
