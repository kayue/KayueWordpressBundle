<?php

namespace Kayue\WordpressBundle\Security\Firewall;

use Kayue\WordpressBundle\Entity\User;
use Kayue\WordpressBundle\Security\Http\WordpressCookieService;
use Kayue\WordpressBundle\Wordpress\AuthenticationCookieManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class WordpressListener implements ListenerInterface
{
    protected $tokenStorage;
    protected $cookieService;
    protected $cookieManager;
    protected $authenticationManager;
    protected $logger;
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param TokenStorageInterface $tokenStorage
     * @param WordpressCookieService $cookieService
     * @param AuthenticationCookieManager $cookieManager
     * @param AuthenticationManagerInterface $authenticationManager
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        WordpressCookieService $cookieService,
        AuthenticationCookieManager $cookieManager,
        AuthenticationManagerInterface $authenticationManager,
        LoggerInterface $logger = null,
        EventDispatcherInterface $dispatcher = null
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->cookieService = $cookieService;
        $this->cookieManager = $cookieManager;
        $this->authenticationManager = $authenticationManager;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handles WordPress's cookie based authentication.
     *
     * Since we assume WordPress is the only authentication system in the firewall, it will clear all previous token.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        if (null !== $this->dispatcher && HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
        }

        // WordPress firewall will clear all previous token.
        $this->tokenStorage->setToken(null);

        $request = $event->getRequest();

        try {
            if (null === $returnValue = $this->attemptAuthentication($request)) {
                return;
            }

            $this->onSuccess($event, $request, $returnValue);
        } catch (AuthenticationException $e) {
            $this->onFailure($event, $request, $e);
        }
    }

    protected function attemptAuthentication(Request $request)
    {
        if (null === $token = $this->cookieService->autoLogin($request)) {
            return null;
        }

        return $this->authenticationManager->authenticate($token);
    }

    private function onSuccess(GetResponseEvent $event, Request $request, TokenInterface $token)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('WordPress user "%s" has been authenticated successfully', $token->getUsername()));
        }

        $this->tokenStorage->setToken($token);

        if (null !== $this->dispatcher) {
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
        }
    }

    private function onFailure($event, $request, $e)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('WordPress authentication failed: %s', $e->getMessage()));
        }

        $this->tokenStorage->setToken(null);
    }

    /**
     * Writes or remove the WordPress cookie.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($token !== null && true === $token->getUser() instanceof User) {
            if (null !== $this->logger) {
                $this->logger->debug('Write WordPress cookie');
            }

            $this->cookieService->loginSuccess($request, $response, $token);
        }

        // Add WordPress cookie to respond.
        if ($request->attributes->has(WordpressCookieService::CLEAR_AUTH_COOKIE_ATTR)) {
            $this->cookieManager->clearCookies($response);
        }
    }
}
