<?php

namespace Kayue\WordpressBundle\Model;

use Doctrine\Common\Cache\ArrayCache;
use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Kayue\WordpressBundle\Event\SwitchBlogEvent;
use Kayue\WordpressBundle\WordpressEvents;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BlogManager implements BlogManagerInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $blogs = array();

    /**
     * @var int
     */
    protected $currentBlogId = 1;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  integer                    $id
     * @throws \Doctrine\ORM\ORMException
     * @return Blog
     */
    public function findBlogById($id)
    {
        if (!isset($this->blogs[$id])) {
            $em = WordpressEntityManager::create(
                $this->container->get('database_connection'),
                $this->container->get('doctrine.orm.entity_manager')->getConfiguration()
            );

            // use brand a new cache each entity manager to prevent faulty cache
            $em->getMetadataFactory()->setCacheDriver(new ArrayCache());

            if (null === $em->getRepository('KayueWordpressBundle:Blog')->findOneBy(array('id'=>$id))) {
                throw new \Doctrine\ORM\ORMException(sprintf('Blog %d was not found.', $id));
            }

            $em->setBlogId($id);

            $blog = new Blog();
            $blog->setId($id);
            $blog->setEntityManager($em);

            $this->blogs[$id] = $blog;
        }

        return $this->blogs[$id];
    }

    public function getCurrentBlog()
    {
        return $this->findBlogById($this->getCurrentBlogId());
    }

    public function getCurrentBlogId()
    {
        return $this->currentBlogId;
    }

    public function setCurrentBlogId($currentBlogId)
    {
        $this->currentBlogId = $currentBlogId;

        $event = new SwitchBlogEvent($this->getCurrentBlog());
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(WordpressEvents::SWITCH_BLOG, $event);
    }
}
