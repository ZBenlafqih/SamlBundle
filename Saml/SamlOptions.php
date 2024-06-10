<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Saml;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Handles the options of firewall
 *
 * @package    SamlBundle
 * @subpackage Saml
 */
class SamlOptions
{
    protected ParameterBag $options;

    public function __construct(array $options = [])
    {
        $this->options = new ParameterBag($options);
    }

    public function getOptions(): ParameterBag
    {
        return $this->options;
    }

    public function getOption(string $optionId): mixed
    {
        return $this->options->get($optionId);
    }
}
