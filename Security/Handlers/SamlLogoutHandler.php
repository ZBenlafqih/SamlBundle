<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Http\HttpUtils;
use PDias\SamlBundle\Saml\SamlAuth;

/**
 * Handles logging out of Saml when the user logs out of Symfony
 *
 * @package    SamlBundle
 * @subpackage Security\Handlers
 */
class SamlLogoutHandler implements LogoutHandlerInterface
{
    protected ParameterBag $options;
    protected SamlAuth $samlAuth;
    protected HttpUtils $httpUtils;

    public function __construct(SamlAuth $samlAuth, HttpUtils $httpUtils, array $options = [])
    {
        $this->samlAuth = $samlAuth;
        $this->httpUtils = $httpUtils;
        $this->options = new ParameterBag($options);
    }

    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        if ($this->samlAuth->isAuthenticated()) {
            if (method_exists($response, 'getTargetUrl')) {
                $this->samlAuth->setLogoutReturn($response->getTargetUrl());
            } else {
                $this->samlAuth->setLogoutReturn($this->httpUtils->generateUri($request, $this->options->get('logout_return')));
            }
            $this->samlAuth->logout();
        }
    }
}
