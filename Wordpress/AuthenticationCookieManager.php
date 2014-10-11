<?php

namespace Kayue\WordpressBundle\Wordpress;

use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthenticationCookieManager
{
    const COOKIE_DELIMITER = '|';

    protected $configuration;
    protected $options;

    public function __construct(ConfigurationManager $configuration, array $options = array())
    {
        $this->configuration = $configuration;
        $this->options = $options;
    }

    /**
     * Validates WordPress authentication cookie
     *
     * @param UserProviderInterface $userProvider
     * @param Cookie $cookie
     * @return UserInterface UserInterface if valid.
     * @throws RuntimeException
     * @throws AuthenticationException
     */
    public function validateCookie(UserProviderInterface $userProvider, $cookie)
    {
        $cookieParts = $this->decodeCookie($cookie);

        switch (count($cookieParts)) {
            case 3:
                list($username, $expiration, $hmac) = $cookieParts;
                $token = null;
                break;
            case 4:
                list($username, $expiration, $token, $hmac) = $cookieParts;
                break;
            default:
                throw new AuthenticationException('Invalid WordPress cookie.');
        }

        if ($expiration < time()) {
            throw new AuthenticationException('The WordPress cookie has expired.');
        }

        try {
            $user = $userProvider->loadUserByUsername($username);
        } catch (Exception $exception) {
            if (!$exception instanceof AuthenticationException) {
                $exception = new AuthenticationException($exception->getMessage(), $exception->getCode(), $exception);
            }

            throw $exception;
        }

        if (!$user instanceof UserInterface) {
            throw new RuntimeException(sprintf('The UserProviderInterface implementation must return an instance of UserInterface, but returned "%s".', get_class($user)));
        }

        if (
            ($token && $hmac !== $this->generateHmacWithToken($username, $expiration, $token, $user->getPassword())) ||
            (!$token && $hmac !== $this->generateHmac($username, $expiration, $user->getPassword()))
        ) {
            throw new AuthenticationException('The WordPress cookie\'s hash is invalid. Your logged in key and salt settings could be wrong.');
        }

        return $user;
    }

    /**
     * Create WordPress logged in cookie
     *
     * @param UserInterface $user
     * @return Cookie
     */
    public function createLoggedInCookie(UserInterface $user)
    {
        $username   = $user->getUsername();
        $password   = $user->getPassword();
        $expiration = time() + $this->options['lifetime'];
        $hmac       = $this->generateHmac($username, $expiration, $password);

        return new Cookie(
            $this->getLoggedInCookieName(),
            $this->encodeCookie(array($username, $expiration, $hmac)),
            time() + $this->options['lifetime'],
            $this->configuration->getCookiePath(),
            $this->configuration->getCookieDomain()
        );
    }

    protected function generateHmac($username, $expires, $password)
    {
        $passwordFrag = substr($password, 8, 4);

        // From wp_salt()
        $salt = $this->configuration->getLoggedInKey() . $this->configuration->getLoggedInSalt();

        // From wp_hash()
        $key = hash_hmac('md5', $username.$passwordFrag.'|'.$expires, $salt);
        $hash = hash_hmac('md5', $username.'|'.$expires, $key);

        var_dump($hash);
        return $hash;
    }

    protected function generateHmacWithToken($username, $expires, $token, $password)
    {
        $passwordFrag = substr($password, 8, 4);

        // From wp_salt()
        $salt = $this->configuration->getLoggedInKey() . $this->configuration->getLoggedInSalt();

        // From wp_hash()
        $key = hash_hmac('md5', $username.'|'.$passwordFrag.'|'.$expires.'|'.$token, $salt);
        $hash = hash_hmac('sha256', $username.'|'.$expires.'|'.$token, $key);

        return $hash;
    }

    public function clearCookies(Response $response)
    {
        $cookies = array(
            $this->getAuthenticationCookieName(),
            $this->getLoggedInCookieName(),
            'wordpress_test_cookie',
        );

        foreach ($cookies as $name) {
            $response->headers->clearCookie($name, $this->getCookiePath(), $this->getCookieDomain());
        }
    }

    public function decodeCookie($rawCookie)
    {
        return explode(self::COOKIE_DELIMITER, $rawCookie);
    }

    public function encodeCookie(array $cookieParts)
    {
        return implode(self::COOKIE_DELIMITER, $cookieParts);
    }

    public function getLoggedInCookieName()
    {
        return 'wordpress_logged_in_'.md5($this->configuration->getSiteUrl());
    }

    public function getAuthenticationCookieName()
    {
        return 'wordpress_'.md5($this->configuration->getSiteUrl());
    }

    protected function getCookiePath()
    {
        return $this->configuration->getCookiePath();
    }

    protected function getCookieDomain()
    {
        return $this->configuration->getCookieDomain();
    }
}
