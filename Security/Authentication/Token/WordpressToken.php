<?php

namespace Kayue\WordpressBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class WordpressToken extends AbstractToken
{
    private $expiration;
    private $hmac;

    /**
     * Constructor.
     */
    public function __construct($username, $expiration, $hmac)
    {
        $this->authenticated = false;
        $this->setUser($username);
        $this->expiration = $expiration;
        $this->hmac = $hmac;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function isExpired()
    {
        return $this->expiration < time();
    }

    public function getHmac()
    {
        return $this->hmac;
    }

    public function getCredentials()
    {
        return '';
    }
}