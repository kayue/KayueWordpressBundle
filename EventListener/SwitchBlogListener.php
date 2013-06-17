<?php

namespace Kayue\WordpressBundle\EventListener;

use Kayue\WordpressBundle\Event\SwitchBlogEvent;
use Kayue\WordpressBundle\Model\AttachmentManager;
use Kayue\WordpressBundle\Model\CommentManager;
use Kayue\WordpressBundle\Model\OptionManager;
use Kayue\WordpressBundle\Model\PostManager;
use Kayue\WordpressBundle\Model\TermManager;
use Kayue\WordpressBundle\Twig\Extension\WordpressExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SwitchBlogListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onSwitchBlog(SwitchBlogEvent $event)
    {
        $this->updateModelManagerServices($event);
        $this->updateWordpressTwigExtension($event);
    }

    private function updateModelManagerServices(SwitchBlogEvent $event)
    {
        $em = $event->getBlog()->getEntityManager();

        $this->container->set('kayue_wordpress.option.manager', new OptionManager($em));
        $this->container->set('kayue_wordpress.post.manager', new PostManager($em));
        $this->container->set('kayue_wordpress.comment.manager', new CommentManager($em));
        $this->container->set('kayue_wordpress.term.manager', new TermManager($em));
        $this->container->set('kayue_wordpress.attachment.manager', new AttachmentManager($em));
    }

    private function updateWordpressTwigExtension(SwitchBlogEvent $event)
    {
        /** @var $extension WordpressExtension */
        $extension = $this->container->get('kayue_wordpress.twig.wordpress');
        $em = $event->getBlog()->getEntityManager();

        $extension->setEntityManager($em);
    }
}