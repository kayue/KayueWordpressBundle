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

    public function testCreateComment()
    {
        $post = new Post();
        $request = new Request();
        $comment = $this->manager->createComment($post, $request);
        $this->assertInstanceOf($this->manager->getClass(), $comment);
    }

    protected function setUp()
    {
        $emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->manager = new CommentManager($emMock);
    }


}
