<?php

namespace Kayue\WordpressBundle\Security\Authentication\Provider;

use Kayue\WordpressBundle\Security\Authentication\Token\WordpressToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class WordpressProvider implements AuthenticationProviderInterface
{
    private $userChecker;

    /**
     * @param UserCheckerInterface $userChecker
     */
    function __construct(UserCheckerInterface $userChecker = null)
    {
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

        $user = $token->getUser();
        $this->userChecker->checkPostAuth($user);

        $token->setAuthenticated(true);

        return $token;
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