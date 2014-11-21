<?php

namespace Kayue\WordpressBundle\Tests\Security\Authentication\Provider;

use Kayue\WordpressBundle\Security\Authentication\Provider\WordpressProvider;

class WordpressProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthenticateWithAnonymousToken()
    {
        $userCheckerMock = $this->getUserCheckerMock();
        $tokenMock = $this->getMock(
            'Symfony\Component\Security\Core\Authentication\Token\AnonymousToken',
            array(),
            array('key', 'user', array())
        );
        $wordpressProvider = new WordpressProvider($userCheckerMock);

        $this->assertNull($wordpressProvider->authenticate($tokenMock));
    }

    public function testAuthenticateWithWordpressToken()
    {
        $userCheckerMock = $this->getUserCheckerMock();
        $userMock = $this->getMock('Kayue\WordpressBundle\Entity\User');
        $userMock->expects($this->any())->method('getRoles')->will($this->returnValue(array('WP_SUBSCRIBER')));
        $tokenMock = $this->getMock(
            'Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken',
            array('getUser'),
            array($userMock, array())
        );
        $tokenMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $wordpressProvider = new WordpressProvider($userCheckerMock);

        $this->assertTrue($wordpressProvider->authenticate($tokenMock)->isAuthenticated());
    }

    private function getUserCheckerMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
    }
}
