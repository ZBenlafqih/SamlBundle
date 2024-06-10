<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for saml
 *
 * @package    SamlBundle
 * @subpackage Twig\Extension
 */
class SamlExtension extends AbstractExtension
{
    protected UrlGeneratorInterface $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('samlLoginUrl', [$this, 'getLoginUrl'], ['is_safe' => ['html']]),
            new TwigFunction('samlLogoutUrl', [$this, 'getLogoutUrl'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        return $this->router->generate('saml_login_check');
    }

    /**
     * @return string
     */
    public function getLogoutUrl(): string
    {
        return $this->router->generate('saml_logout');
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'saml_extension';
    }
}
