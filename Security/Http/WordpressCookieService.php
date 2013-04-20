<?php

namespace Kayue\WordpressBundle\Security\Http;

use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\RememberMe\AbstractRememberMeServices;

class WordpressCookieService extends AbstractRememberMeServices
{
    const COOKIE_DELIMITER = '|';
    private $loggedInKey;
    private $loggedInSalt;
    private $userProvider;
    protected $options;
    protected $logger;

    public function __construct($loggedInKey, $loggedInSalt, UserProviderInterface $userProvider, array $options = array(), LoggerInterface $logger = null)
    {
        $this->loggedInKey = $loggedInKey;
        $this->loggedInSalt = $loggedInSalt;
        $this->userProvider = $userProvider;
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     *
     * @return null           Return null if failed to retrieve token.
     * @return WordpressToken Return WordpressToken if success.
     */
    public function getTokenFromRequest(Request $request)
    {
        if (null === $cookie = $request->cookies->get($this->options['name'])) {
             return null;
        }

        if (null !== $this->logger) {
            $this->logger->debug('WordPress cookie detected.');
        }

        $cookieParts = $this->decodeCookie($cookie);

        try {
            $user = $this->processAutoLoginCookie($cookieParts, $request);

            if (!$user instanceof UserInterface) {
                throw new \RuntimeException('processAutoLoginCookie() must return a UserInterface implementation.');
            }

            if (null !== $this->logger) {
                $this->logger->info('WordPress cookie accepted.');
            }

            return new WordpressToken($user);
        } catch (UsernameNotFoundException $notFound) {
            if (null !== $this->logger) {
                $this->logger->info('User for WordPress cookie not found.');
            }
        } catch (AuthenticationException $invalid) {
            if (null !== $this->logger) {
                $this->logger->debug('WordPress authentication failed: '.$invalid->getMessage());
            }
        }

        $this->cancelCookie($request);

        return null;
    }

    /**
     * Validate the cookie and do any additional processing that is required.
     * This is called from autoLogin().
     *
     * @param array $cookieParts
     * @param Request $request
     *
     * @throws \RuntimeException
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @return TokenInterface
     */
    protected function processAutoLoginCookie(array $cookieParts, Request $request)
    {
        if (count($cookieParts) !== 3) {
            throw new AuthenticationException('The cookie is invalid.');
        }

        list($username, $expiration, $hmac) = $cookieParts;

        try {
            $user = $this->userProvider->loadUserByUsername($username);
        } catch (\Exception $ex) {
            if (!$ex instanceof AuthenticationException) {
                $ex = new AuthenticationException($ex->getMessage(), $ex->getCode(), $ex);
            }

            throw $ex;
        }

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException(sprintf('The UserProviderInterface implementation must return an instance of UserInterface, but returned "%s".', get_class($user)));
        }

        if ($hmac !== $this->generateHmac($username, $expiration, $user->getPassword())) {
            throw new AuthenticationException('The WordPress cookie\'s hash is invalid.');
        }

        if ($expiration < time()) {
            throw new AuthenticationException('The WordPress cookie has expired.');
        }

        return $user;
    }

    /**
     * @param $username
     * @param $expires
     * @param $password
     * @return string
     */
    protected function generateHmac($username, $expires, $password)
    {
        $passwordFrag = substr($password, 8, 4);

        // from wp_salt()
        $salt = $this->loggedInKey.$this->loggedInSalt;

        // from wp_hash()
        $key = hash_hmac('md5', $username.$passwordFrag.'|'.$expires, $salt);
        $hash = hash_hmac('md5', $username.'|'.$expires, $key);

        return $hash;
    }

    /**
     * This is called after a user has been logged in successfully, and has
     * requested WordPress capabilities. The implementation usually sets a
     * cookie and possibly stores a persistent record of it.
     *
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     */
    protected function onLoginSuccess(Request $request, Response $response, TokenInterface $token)
    {
        $user       = $token->getUser();
        $username   = $user->getUsername();
        $password   = $user->getPassword();
        $expiration = time() + $this->options['lifetime'];
        $hmac       = $this->generateHmac($username, $expiration, $password);

        $response->headers->setCookie(
            new Cookie(
                $this->options['name'],
                $this->encodeCookie(array($username, $expiration, $hmac)),
                time() + $this->options['lifetime'],
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'],
                $this->options['httponly']
            )
        );
    }


    /**
     * Deletes the WordPress cookie
     *
     * @param Request $request
     */
    protected function cancelCookie(Request $request)
    {
        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Clearing WordPress cookie "%s"', $this->options['name']));
        }

        // TODO: Clear WordPress backend cookie as well
        $request->attributes->set(self::COOKIE_ATTR_NAME, new Cookie($this->options['name'], null, 1, $this->options['path'], $this->options['domain']));
    }

    /**
     * Decodes the raw cookie value
     *
     * @param string $rawCookie
     *
     * @return array
     */
    protected function decodeCookie($rawCookie)
    {
        return explode(self::COOKIE_DELIMITER, $rawCookie);
    }

    /**
     * Encodes the cookie parts
     *
     * @param array $cookieParts
     *
     * @return string
     */
    protected function encodeCookie(array $cookieParts)
    {
        return implode(self::COOKIE_DELIMITER, $cookieParts);
    }

}