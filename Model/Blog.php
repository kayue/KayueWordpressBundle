<?php

namespace Kayue\WordpressBundle\Model;

use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;

class Blog implements BlogInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var WordpressEntityManager
     */
    protected $entityManager;

    /**
     * @param integer $id
     * @param WordpressEntityManager $entityManager
     */
    public function __construct($id, WordpressEntityManager $entityManager)
    {
        $this->id = $id;
        $this->entityManager = $entityManager;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return WordpressEntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
