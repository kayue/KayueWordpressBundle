<?php

namespace Kayue\WordpressBundle\Tests\Wordpress\Extra;

use Kayue\WordpressBundle\Wordpress\Extra\ExtraTransformerRegistry;

class ExtraTransformerRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testRegistrationOfAnExtraTransformer()
    {
        $mock = $this->getMock('Kayue\WordpressBundle\Wordpress\Extra\ExtraTransformerInterface');
        $mock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue('hello'))
        ;

        $registry = new ExtraTransformerRegistry();
        $registry->addTransformer('test', $mock);

        $content = $registry->transform('something');

        $this->assertEquals($content, 'hello');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRegistrationOfATransformerTwoTimes()
    {
        $registry = new ExtraTransformerRegistry();

        $registry->addTransformer('test', $this->getMock('Kayue\WordpressBundle\Wordpress\Extra\ExtraTransformerInterface'));
        $registry->addTransformer('test', $this->getMock('Kayue\WordpressBundle\Wordpress\Extra\ExtraTransformerInterface'));
    }

    public function testDisableATransformer()
    {
        $mock = $this->getMock('Kayue\WordpressBundle\Wordpress\Extra\ExtraTransformerInterface');
        $mock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue('hello'))
        ;

        $registry = new ExtraTransformerRegistry();
        $registry->addTransformer('test', $mock);
        $registry->disable('test');

        $content = $registry->transform('something');

        $this->assertEquals($content, 'something');
    }
}
