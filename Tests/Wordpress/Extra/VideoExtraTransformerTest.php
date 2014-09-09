<?php


namespace Kayue\WordpressBundle\Tests\Wordpress\Extra;


use Kayue\WordpressBundle\Wordpress\Extra\VideoExtraTransformer;

class VideoExtraTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testYoutubeTransformation()
    {
        $transformer = new VideoExtraTransformer();
        $transformer->setOptions(array(
            'width' => '200',
            'height' => '200'
        ));

        $content = 'Some random text containing a youtube video link like https://www.youtube.com/watch?v=zytefWJVHn0&list=PLXONb89nemXti1Io2AcGzNWv1rf6IwJjh&index=9';
        $content = $transformer->transform($content);

        $this->assertEquals(
            $content,
            'Some random text containing a youtube video link like '.
            '<iframe width="200" height="200" src="//www.youtube.com/embed/zytefWJVHn0" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>'
        );
    }

    public function testVimeoTransformation()
    {
        $transformer = new VideoExtraTransformer();
        $transformer->setOptions(array(
            'width' => '200',
            'height' => '200'
        ));

        $content = 'Some random text containing a youtube video link like http://vimeo.com/channels/staffpicks/104961644';
        $content = $transformer->transform($content);

        $this->assertEquals(
            $content,
            'Some random text containing a youtube video link like '.
            '<iframe src="//player.vimeo.com/video/104961644" width="200" height="200" frameborder="0"  allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>'
        );
    }
}
