<?php

namespace Kayue\WordpressBundle\Tests\Model;

use Kayue\WordpressBundle\Model\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testUsername()
    {
        $user = $this->getUser();
        $this->assertNull($user->getUsername());

        $user->setUsername('tony');
        $this->assertEquals('tony', $user->getUsername());
    }

    public function testEmail()
    {
        $user = $this->getUser();
        $this->assertNull($user->getEmail());

        $user->setEmail('tony@mail.org');
        $this->assertEquals('tony@mail.org', $user->getEmail());
    }

    public function testTrueHasRole()
    {
        $this->markTestIncomplete();

        $user = $this->getUser();

        $defaultrole = User::ROLE_DEFAULT;
        $newrole = 'ROLE_X';

        $this->assertTrue($user->hasRole($defaultrole));

        $user->addRole($defaultrole);
        $this->assertTrue($user->hasRole($defaultrole));

        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    public function testFalseHasRole()
    {
        $this->markTestIncomplete();

        $user = $this->getUser();
        $newrole = 'ROLE_X';

        $this->assertFalse($user->hasRole($newrole));

        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->getMockForAbstractClass('Kayue\WordpressBundle\Model\User');
    }
}