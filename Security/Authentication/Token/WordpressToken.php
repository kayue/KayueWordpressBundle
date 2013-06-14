<?php

namespace Kayue\WordpressBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class WordpressToken extends AbstractToken
{
    /**
     * Constructor.
     */
    public function __construct(UserInterface $user, array $roles = array())
    {
        parent::__construct($user->getRoles());
        $this->setUser($user);
    }

    public function getCredentials()
    {
        return '';
    }
}
