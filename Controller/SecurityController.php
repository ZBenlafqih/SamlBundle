<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * SamlBundle security controller.
 *
 * @package    SamlBundle
 * @subpackage Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login-saml", name="saml_login")
     */
    public function loginAction(Request $request): Response
    {
        return $this->render('@Saml/Security/login.html.twig');
    }

    /**
     * @Route("/login-check-saml", name="saml_check")
     */
    public function checkAction(): void
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using saml in your security firewall configuration.');
    }

    /**
     * @Route("/logout-saml", name="saml_logout")
     */
    public function logoutAction(): void
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
