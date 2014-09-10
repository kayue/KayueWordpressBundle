<?php

namespace Kayue\WordpressBundle\Tests\Model;

use Kayue\WordpressBundle\Model\CommentManager;
use Kayue\WordpressBundle\Model\Post;
use Symfony\Component\HttpFoundation\Request;

class CommentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $manager CommentManager
     */
    public $manager;

    protected function setUp()
    {
        $containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $containerMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('kayue_wordpress.entity_manager'))
            ->will($this->returnValue('default'))
        ;
        $containerMock
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('doctrine.orm.default_entity_manager'))
            ->will($this->returnValue($emMock))
        ;
        $this->manager = new CommentManager($containerMock);
    }

    public function testCreateComment()
    {
        $post = new Post();
        $request = new Request();
        $comment = $this->manager->createComment($post, $request);
        $this->assertInstanceOf($this->manager->getClass(), $comment);
    }
}
