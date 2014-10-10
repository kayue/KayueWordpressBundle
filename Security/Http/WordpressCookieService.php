<?php

namespace Kayue\WordpressBundle\Security\Http;

use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;
use Kayue\WordpressBundle\Wordpress\AuthenticationCookieManager;
use Psr\Log\LoggerInterface;
use RuntimeException;
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
    const CLEAR_AUTH_COOKIE_ATTR = '_wordpress_clear_auth_cookie';

    /**
     * Cookie delimiter.
     *
     * @var string
     */
    const COOKIE_DELIMITER = '|';

    protected $cookieManager;
    protected $userProvider;
    protected $options;
    protected $logger;

    public function __construct(
        AuthenticationCookieManager $cookieManager,
        UserProviderInterface $userProvider,
        array $options = array(),
        LoggerInterface $logger = null
    )
    {
        $this->cookieManager = $cookieManager;
        $this->userProvider = $userProvider;
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     *
     * @throws RuntimeException
     * @return null             Return null if failed to retrieve token.
     * @return WordpressToken   Return WordpressToken if success.
     */
    public function autoLogin(Request $request)
    {
        $loggedInCookieName = $this->cookieManager->getLoggedInCookieName();

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

        try {
            $user = $this->processAutoLoginCookie($cookie);

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
     * @param $cookie
     * @internal param array $cookieParts
     * @internal param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return TokenInterface
     */
    protected function processAutoLoginCookie($cookie)
    {
        return $this->cookieManager->validateCookie($this->userProvider, $cookie);
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
        if ($this->configuration->getVersion() === 4) {
            /**
             * @see https://github.com/WordPress/WordPress/blob/4.0/wp-includes/pluggable.php#L879-L883
             */
            // TODO: Create session token
            
            $user       = $token->getUser();
            $username   = $user->getUsername();
            $password   = $user->getPassword();
            $expiration = time() + $this->options['lifetime'];
            $hmac       = $this->generateHmac($username, $expiration, $password);
            
            $sessionToken = $this->sessionTokenManager->create($expiration);
            $encodedCookie = $this->encodeCookie(array($username, $expiration, $sessionToken, $hmac));
        } else {
            if (false === $request->cookies->has($this->cookieManager->getLoggedInCookieName())) {
                $response->headers->setCookie(
                    $this->cookieManager->createLoggedInCookie($token->getUser(), $this->options['lifetime'])
                );
            }
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
            $this->logger->debug('Clearing WordPress cookies.');
        }

        $request->attributes->set(self::CLEAR_AUTH_COOKIE_ATTR, true);
    }
}
