<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;

interface BlogInterface
{
    /**
     * @param integer $id
     * @param WordpressEntityManager $entityManager
     */
    public function __construct($id, WordpressEntityManager $entityManager);

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return WordpressEntityManager
     */
    public function getEntityManager();
}