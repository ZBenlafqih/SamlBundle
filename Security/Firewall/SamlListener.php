<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent as GetResponseEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface as SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Http\AccessMapInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use PDias\SamlBundle\Security\Authentication\Token\SamlUserToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use PDias\SamlBundle\Saml\SamlAuth;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlListener
{
    protected $tokenStorage;
    protected $authenticationManager;
    protected $accessDecisionManager;
    protected $map;
    protected $samlAuth;
    protected $httpUtils;
    protected $logger;
    protected $options;
    protected $eventDispatcher = null;

    public function __construct(
            TokenStorageInterface $tokenStorage,
            AuthenticationManagerInterface $authenticationManager,
            AccessDecisionManagerInterface $accessDecisionManager,
            AccessMapInterface $map,
            HttpUtils $httpUtils,
            EventDispatcherInterface $eventDispatcher,
            SamlAuth $samlAuth,
            LoggerInterface $logger = null,
            array $options = array())
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->map = $map;
        $this->httpUtils = $httpUtils;
        $this->eventDispatcher = $eventDispatcher;
        $this->samlAuth = $samlAuth;
        $this->logger = $logger;
        $this->options = $options;
    }

    /**
     * @param RequestEvent $event
     * @return TokenInterface|void
     */
    public function __invoke(RequestEvent $event)
    {

        $request = $event->getRequest();
        try {
            $samlToken = new SamlUserToken();
            $samlToken->setDirectEntry($this->options['direct_entry']);

            $authToken = $this->authenticationManager->authenticate($samlToken);

            if ($authToken instanceof TokenInterface ) {
                $this->onSuccess($request, $authToken);

                return $authToken;
            } else if ($authToken instanceof Response) {
                return $event->setResponse($authToken);
            }

        } catch (\Exception $e) {
            $token = $this->tokenStorage->getToken();
            list($attributes) = $this->map->getPatterns($request);

            if (null !== $token && null !== $attributes) {
                if ($token->isAuthenticated() && $this->accessDecisionManager->decide($token, $attributes, $request)) {
                    return;
                }
            }

            $this->requestSaml($request);
            $token = $this->tokenStorage->getToken();
            if ($token instanceof SamlUserToken/* && $this->providerKey === $token->getProviderKey()*/) {
                $this->tokenStorage->setToken(null);
            }
            return;

            //throw new AuthenticationException('The Saml user could not be retrieved from the session.');
        }

        return;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     */
    private function onSuccess(Request $request, TokenInterface $token)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('User "%s" has been authenticated successfully', $token->getUsername()));
        }

        $this->tokenStorage->setToken($token);

        $session = $request->getSession();
        $session->remove(Security::AUTHENTICATION_ERROR);
        $session->remove(Security::LAST_USERNAME);

        if (null !== $this->eventDispatcher) {
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch($loginEvent, SecurityEvents::INTERACTIVE_LOGIN);
        }
    }

    /**
     * @param Request $request
     */
    private function requestSaml(Request $request)
    {
        if($this->options['direct_entry'] || $this->httpUtils->checkRequestPath($request, $this->options['check_path']))
        {
            $this->samlAuth->setLoginReturn($this->getReturnUrl($request));
            $this->samlAuth->requireAuth();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function getReturnUrl(Request $request)
    {
        if($this->options['always_use_default_target_path'] && isset($this->options['default_target_path'])) {
            return $this->httpUtils->generateUri($request, $this->options['default_target_path']);
        }

        //return $this->httpUtils->generateUri($request, $this->options['login_return']);
        return $this->httpUtils->generateUri($request, '/');
    }
}
