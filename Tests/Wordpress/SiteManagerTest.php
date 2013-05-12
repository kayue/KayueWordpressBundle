<?php

namespace Kayue\WordpressBundle\Tests\Wordpress;

use Kayue\WordpressBundle\Wordpress\SiteManager;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

class SiteManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testGetCurrentSite($name, $hostname, $path, $fail = false)
    {
        $requestMock = $this->getMock(
            'Symfony\Component\HttpFoundation\Request',
            array('getPathInfo', 'getHost')
        );
        $requestMock->expects($this->any())
            ->method('getPathInfo')
            ->withAnyParameters()
            ->will($this->returnValue($path));
        $requestMock->expects($this->any())
            ->method('getHost')
            ->withAnyParameters()
            ->will($this->returnValue($hostname));

        $requestContext = new RequestContext('', 'GET', $hostname);

        $routerMock = $this->getMockBuilder('Symfony\Component\Routing\Router')->disableOriginalConstructor()->getMock();
        $routerMock->expects($this->any())
            ->method('getContext')
            ->withAnyParameters()
            ->will($this->returnValue($requestContext));

        $manager = new SiteManager($this->getSites(), $requestMock);

        if(!$fail) {
            $this->assertEquals($name, $manager->getCurrentSite());
        } else {
            try {
                $this->assertEquals($name, $manager->getCurrentSite());
            } catch (ResourceNotFoundException $expected) {
                return;
            }

            $this->fail('An expected exception has not been raised.');
        }
    }

    public function provider()
    {
        return array(
            array('default', 'example.com', '/'),
            array('hostname', 'en.example.com', '/foo'),
            array('placeholder', 'cn.example.com', '/foo/bar'),
            array('requirements', 'example.com.en', '/'),
            array('fail', 'example.com', '', true),
            array('fail', 'example.net', '/', true),
            array('fail', 'example.com.hk', '/', true),
        );
    }

    private function getSites()
    {
        return array(
            'default' => array(
                'hostname_pattern' => 'example.com',
                'entity_manager'   => 'default',
            ),
            'hostname' => array(
                'hostname_pattern' => 'en.example.com',
                'entity_manager'   => 'default',
            ),
            'placeholder' => array(
                'hostname_pattern' => '{locale}.example.com',
                'entity_manager'   => 'default',
            ),
            'requirements' => array(
                'hostname_pattern' => 'example.com.{locale}',
                'requirements'     => array('locale' => 'en|fr'),
                'entity_manager'   => 'default',
            ),
        );
    }
}
