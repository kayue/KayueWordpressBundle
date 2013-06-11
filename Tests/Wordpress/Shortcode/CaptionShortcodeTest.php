<?php

namespace Kayue\WordpressBundle\Tests\Wordpress\Shortcode;

use Kayue\WordpressBundle\Wordpress\Shortcode\CaptionShortcode;
use Kayue\WordpressBundle\Wordpress\Shortcode\ShortcodeChain;

class CaptionShortcodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShortcodeChain
     */
    public $chain;

    protected function setUp()
    {
        $this->chain = new ShortcodeChain();
        $this->chain->addShortcode(new CaptionShortcode());
    }

    public function testSingleCaption()
    {
        $result = $this->chain->process('[caption width="600" caption="A football video"]<img src="http://placehold.it/300x200" />[/caption]');

        $this->assertEquals('<div class="wp-caption alignnone" style="width: 610px"><img src="http://placehold.it/300x200" /><p class="wp-caption-text">A football video</p></div>', $result);
    }

    public function testMultipleCaption()
    {
        $result = $this->chain->process('
        [caption width="400" caption="A football video"]<img src="http://placehold.it/100x200" />[/caption]
        [caption width="300" caption="A football video"]<img src="http://placehold.it/200x200" />[/caption]
        ');

        $this->assertEquals('
        <div class="wp-caption alignnone" style="width: 410px"><img src="http://placehold.it/100x200" /><p class="wp-caption-text">A football video</p></div>
        <div class="wp-caption alignnone" style="width: 310px"><img src="http://placehold.it/200x200" /><p class="wp-caption-text">A football video</p></div>
        ', $result);
    }

    public function testCaptionWithNoWidth()
    {
        $result = $this->chain->process('[caption]<img src="http://placehold.it/300x200" />[/caption]');

        $this->assertEquals('<img src="http://placehold.it/300x200" />', $result);
    }
}
