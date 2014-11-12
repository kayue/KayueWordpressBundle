<?php

namespace Kayue\WordpressBundle;

class WordpressEvents
{
    /**
     * Switch the current blog.
     *
     * @var string
     */
    const SWITCH_BLOG = 'wordpress.blog.switch_blog';

    /**
     * Create new entty manager.
     *
     * @var string
     */
    const CREATE_ENTITY_MANAGER = 'wordpress.entity_manager.create';
}
