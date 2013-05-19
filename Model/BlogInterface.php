<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;

interface BlogInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return WordpressEntityManager
     */
    public function getEntityManager();
}
