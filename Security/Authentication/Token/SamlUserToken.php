<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlUserToken extends AbstractToken
{
    private bool $directEntry = true;

    public function getCredentials(): string
    {
        return '';
    }

    public function setDirectEntry(bool $directEntry): self
    {
        $this->directEntry = $directEntry;
        return $this;
    }

    public function getDirectEntry(): bool
    {
        return $this->directEntry;
    }

    public function isDirectEntry(): bool
    {
        return $this->directEntry;
    }
}
