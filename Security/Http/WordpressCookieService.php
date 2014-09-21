<?php

namespace Kayue\WordpressBundle\Security\Http;

use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;
use Kayue\WordpressBundle\Wordpress\ConfigurationManager;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class WordpressCookieService
{
    /**
     * This attribute name can be used by the implementation if it needs to set
     * a cookie on the Request when there is no actual Response, yet.
     *
     * @var string
     */
    const COOKIE_ATTR_NAME = '_wordpress_logged_in_cookie';

    /**
     * Cookie delimiter.
     *
     * @var string
     */
    const COOKIE_DELIMITER = '|';

    protected $configuration;
    private $userProvider;
    protected $options;
    protected $logger;

    public function __construct(ConfigurationManager $configuration, UserProviderInterface $userProvider, array $options = array(), LoggerInterface $logger = null)
    {
        $this->configuration = $configuration;
        $this->userProvider = $userProvider;
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     *
     * @throws RuntimeException
     * @return null           Return null if failed to retrieve token.
     * @return WordpressToken Return WordpressToken if success.
     */
    public function autoLogin(Request $request)
    {
        $loggedInCookieName = $this->configuration->getLoggedInCookieName();

        // Debug information
        if (null === $cookie = $request->cookies->get($loggedInCookieName)) {
            if (null !== $this->logger) {
                foreach ($request->cookies->keys() as $key) {
                    if (strpos($key, 'wordpress_logged_in_') === 0) {
                        $this->logger->debug(sprintf(
                            'WordPress cookie "%s" detected but does not match with the bundle\'s configuration "%s". Please
                             double check your site url settings.',
                            $key,
                            $loggedInCookieName
                        ));

                        return null;
                    }
                }

                $this->logger->debug('No WordPress cookie detected.');
            }

            return null;
        }

        if (null !== $this->logger) {
            $this->logger->debug('WordPress cookie detected.');
        }

        $cookieParts = $this->decodeCookie($cookie);

        try {
            $user = $this->processAutoLoginCookie($cookieParts, $request);

            if (!$user instanceof UserInterface) {
                throw new RuntimeException('processAutoLoginCookie() must return a UserInterface implementation.');
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
     * @param array   $cookieParts
     * @param Request $request
     *
     * @throws RuntimeException
     * @throws AuthenticationException
     * @return TokenInterface
     */
    protected function processAutoLoginCookie(array $cookieParts, Request $request)
    {
        $count = count($cookieParts);

        switch ($count) {
            case 3:
                list($username, $expiration, $hmac) = $cookieParts;
                break;
            case 4:
                list($username, $expiration, $token, $hmac) = $cookieParts;
                break;
            default:
                throw new AuthenticationException('Invalid WordPress cookie.');
        }

        try {
            $user = $this->userProvider->loadUserByUsername($username);
        } catch (\Exception $ex) {
            if (!$ex instanceof AuthenticationException) {
                $ex = new AuthenticationException($ex->getMessage(), $ex->getCode(), $ex);
            }

            throw $ex;
        }

        if ($expiration < time()) {
            throw new AuthenticationException('The WordPress cookie has expired.');
        }

        if (!$user instanceof UserInterface) {
            throw new RuntimeException(sprintf('The UserProviderInterface implementation must return an instance of UserInterface, but returned "%s".', get_class($user)));
        }

        if (isset($token)) {
            // WordPress 4
            if ($hmac !== $this->generateHmacWithToken($username, $expiration, $token, $user->getPassword())) {
                throw new AuthenticationException('The WordPress cookie\'s hash is invalid. Your logged in key and salt settings could be wrong.');
            }
        } else {
            // WordPress 3
            if ($hmac !== $this->generateHmac($username, $expiration, $user->getPassword())) {
                throw new AuthenticationException('The WordPress cookie\'s hash is invalid. Your logged in key and salt settings could be wrong.');
            }
        }

        return $user;
    }

    protected function generateHmac($username, $expires, $password)
    {
        $passwordFrag = substr($password, 8, 4);

        // from wp_salt()
        $salt = $this->configuration->getLoggedInKey() . $this->configuration->getLoggedInSalt();

        // from wp_hash()
        $key = hash_hmac('md5', $username.$passwordFrag.'|'.$expires, $salt);
        $hash = hash_hmac('md5', $username.'|'.$expires, $key);

        return $hash;
    }

    protected function generateHmacWithToken($username, $expires, $token, $password)
    {
        $passwordFrag = substr($password, 8, 4);

        // from wp_salt()
        $salt = $this->configuration->getLoggedInKey() . $this->configuration->getLoggedInSalt();

        // from wp_hash()
        $key = hash_hmac('md5', $username.'|'.$passwordFrag.'|'.$expires.'|'.$token, $salt);
        $hash = hash_hmac('sha256', $username.'|'.$expires.'|'.$token, $key);

        return $hash;
    }

    /**
     * This is called after a user has been logged in successfully, and has
     * requested WordPress capabilities. The implementation usually sets a
     * cookie and possibly stores a persistent record of it.
     *
     * @param Request        $request
     * @param Response       $response
     * @param TokenInterface $token
     */
    public function loginSuccess(Request $request, Response $response, TokenInterface $token)
    {
        $user       = $token->getUser();
        $username   = $user->getUsername();
        $password   = $user->getPassword();
        $expiration = time() + $this->options['lifetime'];
        $hmac       = $this->generateHmac($username, $expiration, $password);

        if (false === $request->cookies->has($this->configuration->getLoggedInCookieName())) {
            $response->headers->setCookie(
                new Cookie(
                    $this->configuration->getLoggedInCookieName(),
                    $this->encodeCookie(array($username, $expiration, $hmac)),
                    time() + $this->options['lifetime'],
                    $this->configuration->getCookiePath(),
                    $this->configuration->getCookieDomain()
                )
            );
        }
    }

    /**
     * Deletes the WordPress cookie
     *
     * @param Request $request
     */
    public function cancelCookie(Request $request)
    {
        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Clearing WordPress cookie "%s"', $this->configuration->getLoggedInCookieName()));
        }

        // TODO: Clear WordPress backend cookie as well
        $request->attributes->set(self::COOKIE_ATTR_NAME,
            new Cookie(
                $this->configuration->getLoggedInCookieName(),
                null,
                1,
                $this->configuration->getCookiePath(),
                $this->configuration->getCookieDomain()
            )
        );
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
