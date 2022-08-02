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
 * @author Paulo Dias <dias.paulo@gmail.com>
 */
class SecurityController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/login-saml")
     * @return Response
     */
    public function loginAction(Request $request)
    {
        return $this->render('@Saml/Security/login.html.twig');
    }

    /**
     * @param Request $request
     * @Route("/login-check-saml")
     * @return Response
     */
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using saml in your security firewall configuration.');
    }

    /**
     * @param Request $request
     * @Route("/logout-saml")
     * @return Response
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
