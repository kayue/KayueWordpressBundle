<?php

namespace Kayue\WordpressBundle\Security\Authentication\Provider;

use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class WordpressProvider implements AuthenticationProviderInterface
{
    private $loggedInKey;
    private $loggedInSalt;
    private $userProvider;
    private $userChecker;

    /**
     * @param $loggedInKey string The WordPress logged in key
     * @param $loggedInSalt string The WordPress logged in salt
     * @param UserProviderInterface $userProvider
     * @param UserCheckerInterface $userChecker
     */
    function __construct($loggedInKey, $loggedInSalt, UserProviderInterface $userProvider, UserCheckerInterface $userChecker = null)
    {
        $this->loggedInKey = $loggedInKey;
        $this->loggedInSalt = $loggedInSalt;
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
    }

    /**
     * Attempts to authenticates a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        /** @var $token WordpressToken */

        // Check expiration
        if ($token->isExpired()) {
            throw new AuthenticationException('The WordPress login cookie has expired.');
        }

        // Check user existence
        if (null === $user = $this->userProvider->loadUserByUsername($token->getUsername())) {
            throw new AuthenticationException('Invalid WordPress login cookie, user doesn\'t exist.');
        }

        $token->setUser($user);

        // Verify HMAC
        if ($token->getHmac() !== $this->generateHmac($token)) {
            throw new AuthenticationException("Invalid WordPress login cookie, HMAC doesn't match.");
        }

        if($this->userChecker) {
            $this->userChecker->checkPostAuth($user);
        }

        $token->setAuthenticated(true);

        return $token;
    }

    /**
     * Generate HMAC
     *
     * @param WordpressToken $token
     *
     * @return string
     */
    private function generateHmac(WordpressToken $token)
    {
        /** @var $user \Symfony\Component\Security\Core\User\UserInterface*/
        $user = $token->getUser();

        $passwordFrag = substr($user->getPassword(), 8, 4);

        // from wp_salt()
        $salt = $this->loggedInKey.$this->loggedInSalt;

        // from wp_hash()
        $key = hash_hmac('md5', $user->getUsername().$passwordFrag.'|'.$token->getExpiration(), $salt);
        $hash = hash_hmac('md5', $user->getUsername().'|'.$token->getExpiration(), $key);

        return $hash;
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return Boolean true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof WordpressToken;
    }
}