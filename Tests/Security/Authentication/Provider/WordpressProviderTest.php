<?php

namespace Kayue\WordpressBundle\Tests\Security\Authentication\Provider;

use Kayue\WordpressBundle\Security\Authentication\Provider\WordpressProvider;
use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class WordpressProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testNotSupportedToken()
    {
        $userProvider = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserProviderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationProvider = new WordpressProvider('key', 'salt', $userProvider);
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertNull($authenticationProvider->authenticate($token));
    }

    public function testExpiredToken()
    {
        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $tokenMock = $this->getMockBuilder('Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenMock->expects($this->once())
            ->method('isExpired')
            ->will($this->returnValue(true));

        $authenticationProvider = new WordpressProvider('key', 'salt', $userProviderMock);

        try {
            $authenticationProvider->authenticate($tokenMock);
        } catch(AuthenticationException $e) {
            $this->assertEquals('The WordPress login cookie has expired.', $e->getMessage());
        }
    }

    public function testUserNotFound()
    {
        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('mary')
            ->will($this->returnValue(null));

        $authenticationProvider = new WordpressProvider('key', 'salt', $userProviderMock);

        try {
            $authenticationProvider->authenticate(new WordpressToken('mary', time()+1000, 'hmac'));
        } catch(AuthenticationException $e) {
            $this->assertEquals('Invalid WordPress login cookie, user doesn\'t exist.', $e->getMessage());
        }
    }

    public function testAuthenticate()
    {
        $userMock = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $userMock->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('admin'));
        $userMock->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue('$P$BLfimWPNEsJCMh1NyKprIBjHIYtBbu/'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('admin')
            ->will($this->returnValue($userMock));

        $tokenMock = $this->getMock('Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken',
            ['isExpired'],
            ['admin','1366613007','8092029696a419b5b024fbe90eb61888']
        );
        $tokenMock->expects($this->once())
            ->method('isExpired')
            ->will($this->returnValue(false));

        $key = ':j$_=(:l@8Fku^U;MQ~#VOJXOZcVB_@u+t-NNYqmTH4na|)5Bhs1|tF1IA|>tz*E';
        $salt = ')A^CQ<R:1|^dK/Q;.QfP;U!=J=(_i6^s0f#2EIbGIgFN{,3U9H$q|o/sJfWF`NRM';
        $authenticationProvider = new WordpressProvider($key, $salt, $userProviderMock);

        $newToken = $authenticationProvider->authenticate($tokenMock);

        $this->assertTrue($newToken->isAuthenticated());
    }
}