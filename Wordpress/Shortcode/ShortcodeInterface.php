<?php

namespace Kayue\WordpressBundle\Wordpress\Shortcode;

interface ShortcodeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param array  $attr    Attributes of the shortcode
     * @param string $content Content of the shortcode if it is composed of open tag and end tag
     * @return string         A simple string containing html that will replace the shortcode
     */
    public function process(array $attr, $content = null);
}
