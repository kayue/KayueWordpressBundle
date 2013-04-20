<?php

namespace Kayue\WordpressBundle\Security\Firewall;

use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Psr\Log\LoggerInterface;

class WordpressListener extends AbstractAuthenticationListener
{
    /**
     * Performs authentication.
     *
     * @param Request $request A Request instance
     *
     * @return WordpressToken|null The authenticated token, null if full authentication is not possible, or a Response
     *
     * @throws AuthenticationException if the authentication fails
     */
    protected function attemptAuthentication(Request $request)
    {
        // TODO: Fix cookie name
        if (null === $cookie = $request->cookies->get('wordpress_logged_in_xxx')) {
            return null;
        }

        if (null !== $this->logger) {
            $this->logger->debug('WordPress cookie detected.');
        }

        // create the WordpressToken from cookie
        list($username, $expiration, $hmac) = $this->decodeCookie($cookie);

        return $this->authenticationManager->authenticate(new WordpressToken($username, $expiration, $hmac));
    }

    /**
     * Encodes the cookie parts
     *
     * @param array $cookieParts
     *
     * @return string
     */
    private function encodeCookie(array $cookieParts)
    {
        return implode('|', $cookieParts);
    }

    /**
     * Decodes the raw WordPress cookie value
     *
     * @param $rawCookie
     *
     * @return array
     *
     * @throws AuthenticationException
     */
    private function decodeCookie($rawCookie)
    {
        $cookieParts = explode('|', $rawCookie);

        if (count($cookieParts) !== 3) {
            throw new AuthenticationException('The WordPress cookie is invalid.');
        }

        return $cookieParts;
    }
}