<?php

namespace Kayue\WordpressBundle\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;

class WordpressEntityManager extends \Doctrine\ORM\EntityManager
{
    protected $blogId = 1;

    /**
     * @param int $blogId
     */
    public function setBlogId($blogId)
    {
        $this->blogId = $blogId;
    }

    /**
     * @return int
     */
    public function getBlogId()
    {
        return $this->blogId;
    }

    /**
     * Factory method to create EntityManager instances.
     *
     * @param mixed $conn An array with the connection parameters or an existing
     *      Connection instance.
     * @param Configuration $config The Configuration instance to use.
     * @param EventManager $eventManager The EventManager instance to use.
     * @return WordpressEntityManager The created EntityManager.
     */
    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        return new WordpressEntityManager($conn, $config, $conn->getEventManager());
    }
}