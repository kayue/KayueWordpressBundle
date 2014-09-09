<?php

namespace Kayue\WordpressBundle\Tests\Subscriber;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Kayue\WordpressBundle\Subscriber\TablePrefixSubscriber;

class TablePrefixSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../Annotation/WPTable.php');
    }

    public function testLoadClassMetadataWithWordpressBundleEntity()
    {
        $subscriber = new TablePrefixSubscriber('wp_');
        $em = $this->getEntityManagerMock();
        $metadataInfo = $this->getClassMetadataInfoMock();
        $metadataInfo->expects($this->once())->method('setPrimaryTable');
        $args = new LoadClassMetadataEventArgs($metadataInfo, $em);

        $subscriber->loadClassMetadata($args);
    }

    public function testLoadClassMetadataWithOtherEntity()
    {
        $subscriber = new TablePrefixSubscriber('wp_');
        $em = $this->getEntityManagerMock();
        $metadataInfo = $this->getClassMetadataInfoMock(false);
        $metadataInfo->expects($this->never())->method('setPrimaryTable');
        $args = new LoadClassMetadataEventArgs($metadataInfo, $em);

        $subscriber->loadClassMetadata($args);
    }

    public function testPrefixWithNormalEntityManager()
    {
        $subscriber = new TablePrefixSubscriber('other_');

        $em = $this->getEntityManagerMock();

        $metadataInfo = $this->getClassMetadataInfoMock();
        $metadataInfo->name = 'Kayue\WordpressBundle\Entity\Post';
        $metadataInfo->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('posts'));
        $metadataInfo->expects($this->once())
            ->method('setPrimaryTable')
            ->with(array('name'=>'other_posts'));

        $args = new LoadClassMetadataEventArgs($metadataInfo, $em);

        $subscriber->loadClassMetadata($args);
    }

    /**
     * @dataProvider wordpressEntitiesProvider
     */
    public function testTablePrefix($blogId, $entityName, $tableName, $result)
    {
        $subscriber = new TablePrefixSubscriber('wp_');

        $em = $this->getWordpressEntityManagerMock();
        $em->expects($this->any())
            ->method('getBlogId')
            ->will($this->returnValue($blogId));

        $metadataInfo = $this->getClassMetadataInfoMock();
        $metadataInfo->name = "Kayue\\WordpressBundle\\Entity\\{$entityName}";
        $metadataInfo->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));
        $metadataInfo->expects($this->once())
            ->method('setPrimaryTable')
            ->with(array('name'=>"wp_{$result}"));

        $args = new LoadClassMetadataEventArgs($metadataInfo, $em);

        $subscriber->loadClassMetadata($args);
    }

    public function wordpressEntitiesProvider()
    {
        return array(
            array(1, 'User', 'users', 'users'),
            array(1, 'UserMeta', 'usermeta', 'usermeta'),
            array(1, 'Post', 'posts', 'posts'),
            array(1, 'Term', 'terms', 'terms'),
            array(2, 'User', 'users', 'users'),
            array(2, 'UserMeta', 'usermeta', 'usermeta'),
            array(2, 'Post', 'posts', '2_posts'),
            array(2, 'Term', 'terms', '2_terms'),
        );
    }

    private function getClassMetadataInfoMock($wordPressAnnotated = true)
    {
        $mock = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if ($wordPressAnnotated) {
            $mock
                ->expects($this->any())
                ->method('getReflectionClass')
                ->will($this->returnValue(new \ReflectionClass('Kayue\WordpressBundle\Tests\Fixture\Sample')))
            ;
        } else {
            $mock
                ->expects($this->any())
                ->method('getReflectionClass')
                ->will($this->returnValue(new \ReflectionClass('Kayue\WordpressBundle\Tests\Fixture\SampleWithoutAnnotation')))
            ;
        }

        return $mock;
    }

    private function getEntityManagerMock()
    {
        return $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getWordpressEntityManagerMock()
    {
        return $this->getMockBuilder('Kayue\WordpressBundle\Doctrine\WordpressEntityManager')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
