<?php

namespace Kayue\WordpressBundle\Wordpress\Shortcode;

class ShortcodeChain
{
    /**
     * @var ShortcodeInterface[]
     */
    protected $shortcodes = array();

    public function __construct()
    {
        $this->shortcodes = array();
    }

    public function addShortcode(ShortcodeInterface $shortcode)
    {
        $this->shortcodes[] = $shortcode;
    }

    public function doShortcode($content)
    {
        foreach($this->shortcodes as $shortcode) {
            $content = $shortcode->doShortcode($content);
        }

        return $content;
    }
}