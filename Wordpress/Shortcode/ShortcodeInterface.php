<?php

namespace Kayue\WordpressBundle\Wordpress\Shortcode;

interface ShortcodeInterface
{
    public function getName();

    public function doShortcode();
}