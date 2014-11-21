<?php

namespace Kayue\WordpressBundle\Security\Http\Logout;

use Kayue\WordpressBundle\Wordpress\AuthenticationCookieManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class WordpressCookieClearingLogoutHandler implements LogoutHandlerInterface
{
    protected $cookieManager;

    public function __construct(AuthenticationCookieManager $cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->cookieManager->clearCookies($response);
    }
}
