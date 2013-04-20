<?php

namespace Kayue\WordpressBundle\Security\Firewall;

use Kayue\WordpressBundle\Security\Http\WordpressCookieService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
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
     * Handles remember-me cookie based authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        if (null !== $this->securityContext->getToken()) {
            return;
        }

        $request = $event->getRequest();
        if (null === $token = $this->cookieService->getTokenFromRequest($request)) {
            return;
        }

        try {
            $token = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($token);

            if (null !== $this->dispatcher) {
                $loginEvent = new InteractiveLoginEvent($request, $token);
                $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
            }

            if (null !== $this->logger) {
                $this->logger->debug('SecurityContext populated with WordPress token.');
            }
        } catch (AuthenticationException $failed) {
            if (null !== $this->logger) {
                $this->logger->warning(
                    'SecurityContext not populated with WordPress token as the'
                        .' AuthenticationManager rejected the AuthenticationToken returned'
                        .' by the RememberMeServices: '.$failed->getMessage()
                );
            }

            $this->cookieService->loginFail($request);
        }
    }
}