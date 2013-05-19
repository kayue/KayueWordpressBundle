<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;

interface BlogManagerInterface
{
    /**
     * @param integer $name
     * @return Blog
     */
    public function findBlogById($id);
}
