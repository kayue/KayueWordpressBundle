<?php

namespace Kayue\WordpressBundle\Event;

use Kayue\WordpressBundle\Model\Blog;
use Symfony\Component\EventDispatcher\Event;

class SwitchBlogEvent extends Event
{
    protected $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function getBlog()
    {
        return $this->blog;
    }
}