<?php

namespace Kayue\WordpressBundle\Wordpress;

class ConfigurationManager
{
    protected $siteUrl;
    protected $cookiePath;
    protected $cookieDomain;
    protected $loggedInKey;
    protected $loggedInSalt;
    protected $version;

    public function __construct(
        $siteUrl,
        $cookiePath = '/',
        $cookieDomain = null,
        $loggedInKey,
        $loggedInSalt,
        $version = 3
    )
    {
        $this->siteUrl = $siteUrl;
        $this->cookiePath = $cookiePath;
        $this->cookieDomain = $cookieDomain;
        $this->loggedInKey = $loggedInKey;
        $this->loggedInSalt = $loggedInSalt;
        $this->version = $version;
    }

    public function getCookieDomain()
    {
        return $this->cookieDomain;
    }

    public function getCookiePath()
    {
        return $this->cookiePath;
    }

    public function getLoggedInKey()
    {
        return $this->loggedInKey;
    }

    public function getLoggedInSalt()
    {
        return $this->loggedInSalt;
    }

    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
