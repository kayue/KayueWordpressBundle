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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param WordpressEntityManager $manager
     */
    public function setEntityManager(WordpressEntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    /**
     * @return WordpressEntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
