<?php

namespace Kayue\WordpressBundle\Tests\Model;

use Kayue\WordpressBundle\Model\CommentManager;

class CommentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $manager CommentManager
     */
    public $manager;

    public function testCreateComment()
    {
        $comment = $this->manager->createComment();
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
