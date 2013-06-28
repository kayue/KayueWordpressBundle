<?php

namespace Kayue\WordpressBundle\Tests\Model;

use Kayue\WordpressBundle\Entity\UserMeta;
use Kayue\WordpressBundle\Entity\User;

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

    public function testAddRole()
    {
        /** @var $user User */
        $user = new User();
        $meta = new UserMeta();
        $meta->setKey('wp_capabilities');
        $meta->setValue(array('X' => true));
        $user->addMeta($meta);

        $this->assertContains('ROLE_WP_X', $user->getRoles());

        return $user;
    }

    /**
     * @depends testAddRole
     */
    public function testAddAnotherRole(User $user)
    {
        // get existing capabilities
        /** @var $capabilities UserMeta */
        $capabilities = $user->getMetas()->filter(function(UserMeta $meta) {
            return $meta->getKey() === 'wp_capabilities';
        })->first();

        $capabilities->setValue(array_merge($capabilities->getValue(), array('Y' => true)));

        $this->assertContains('ROLE_WP_X', $user->getRoles());
        $this->assertContains('ROLE_WP_Y', $user->getRoles());

        return $user;
    }

    /**
     * @depends testAddAnotherRole
     */
    public function testRemoveRole(User $user)
    {
        // get existing capabilities
        /** @var $capabilities UserMeta */
        $capabilities = $user->getMetas()->filter(function(UserMeta $meta) {
            return $meta->getKey() === 'wp_capabilities';
        })->first();

        $value = $capabilities->getValue();
        $capabilities->setValue($value['X']);

        $this->assertContainsOnly('ROLE_WP_X', $user->getRoles());
    }

    public function testNoRole()
    {
        $user = new User();

        $this->assertEmpty($user->getRoles());
    }

    public function testEqualUser()
    {
        $foo = new User();
        $foo->setUsername('Foo');

        $bar = new User();
        $bar->setUsername('Foo');

        $this->assertTrue($foo->equals($bar));
    }

    public function testNotEqualUser()
    {
        $foo = new User();
        $foo->setUsername('Foo');

        $bar = new User();
        $bar->setUsername('Bar');

        $this->assertFalse($foo->equals($bar));
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->getMockForAbstractClass('Kayue\WordpressBundle\Model\User');
    }
}