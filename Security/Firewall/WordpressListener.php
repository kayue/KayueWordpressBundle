<?php

namespace Kayue\WordpressBundle\Security\Firewall;

use Kayue\WordpressBundle\Model\UserInterface as WordpressUserInterface;
use Kayue\WordpressBundle\Security\Http\WordpressCookieService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

class WordpressListener implements ListenerInterface
{
    private $securityContext;
    private $cookieService;
    private $authenticationManager;
    private $logger;
    private $dispatcher;

    /**
     * Constructor
     *
     * @param SecurityContextInterface $securityContext
     * @param WordpressCookieService $cookieService
     * @param AuthenticationManagerInterface $authenticationManager
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(SecurityContextInterface $securityContext, WordpressCookieService $cookieService, AuthenticationManagerInterface $authenticationManager, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->cookieService = $cookieService;
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
        $this->securityContext->setToken(null);

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

        $this->securityContext->setToken($token);

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

        $this->securityContext->setToken(null);
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

        $token = $this->securityContext->getToken();
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (null === $token || false === $token->getUser() instanceof WordpressUserInterface) {
            if (null !== $this->logger) {
                $this->logger->debug('Remove WordPress cookie');
            }

            $this->cookieService->cancelCookie($request);
        } else {
            if (null !== $this->logger) {
                $this->logger->debug('Write WordPress cookie');
            }

            $this->cookieService->loginSuccess($request, $response, $token);
        }

        // Add WordPress cookie to respond.
        if ($request->attributes->has(WordpressCookieService::COOKIE_ATTR_NAME)) {
            $response->headers->setCookie($request->attributes->get(WordpressCookieService::COOKIE_ATTR_NAME));
        }
    }
}