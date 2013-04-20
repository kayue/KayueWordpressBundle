<?php

namespace Kayue\WordpressBundle\Tests\Security\Authentication\Token;

use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;

class WordpressTokenTest extends \PHPUnit_Framework_TestCase {

    public function testExpiredToken()
    {
        $token = new WordpressToken('username', 1000, 'hmac');
        $this->assertTrue($token->isExpired());
    }

    public function testNotExpiredToken()
    {
        $token = new WordpressToken('username', time()+1000, 'hmac');
        $this->assertFalse($token->isExpired());
    }
}
