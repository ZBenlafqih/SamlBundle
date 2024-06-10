<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Saml;

use SimpleSAML\Auth\Simple;

/**
 * Handles the class SimpleSAML_Auth_Simple
 *
 * @package    SamlBundle
 * @subpackage Saml
 */
class SamlAuth
{
    protected string $provider;
    protected ?string $loginreturn = null;
    protected ?string $logoutreturn = null;
    protected bool $keeppost = true;
    protected Simple $auth;
    protected string $authentication_field = 'mail';

    public function __construct(string $provider)
    {
        $this->provider = $provider;
        $this->auth = new Simple($this->provider);
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        $this->auth = new Simple($this->provider);
        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setLoginReturn(?string $loginreturn): self
    {
        $this->loginreturn = $loginreturn;
        return $this;
    }

    public function getLoginReturn(): ?string
    {
        return $this->loginreturn;
    }

    public function setLogoutReturn(?string $logoutreturn): self
    {
        $this->logoutreturn = $logoutreturn;
        return $this;
    }

    public function getLogoutReturn(): ?string
    {
        return $this->logoutreturn;
    }

    public function setKeepPost(bool $keeppost): self
    {
        $this->keeppost = $keeppost;
        return $this;
    }

    public function getKeepPost(): bool
    {
        return $this->keeppost;
    }

    public function isAuthenticated(): bool
    {
        return $this->auth->isAuthenticated();
    }

    public function requireAuth(): void
    {
        $options = ['KeepPost' => $this->keeppost];
        if ($this->loginreturn) {
            $options = \array_merge($options, ['ReturnTo' => $this->loginreturn]);
        }

        $this->auth->requireAuth($options);
    }

    public function logout(): void
    {
        if ($this->logoutreturn) {
            $this->auth->logout($this->logoutreturn);
        } else {
            $this->auth->logout();
        }
    }

    public function getAttributes(): array
    {
        return $this->auth->getAttributes();
    }

    public function getLoginURL(): string
    {
        return $this->auth->getLoginURL();
    }

    public function getLogoutURL(): string
    {
        return $this->auth->getLogoutURL();
    }

    public function getAuthenticationField(): string
    {
        if ($this->isAuthenticated()) {
            if (\array_key_exists($this->authentication_field, $this->getAttributes())) {
                return $this->authentication_field;
            } else {
                throw new \InvalidArgumentException(sprintf('Your provider must return attribute "%s".', $this->authentication_field));
            }
        }

        return $this->authentication_field;
    }

    public function setAuthenticationField(string $authenticationField): self
    {
        if ($this->isAuthenticated()) {
            if (\array_key_exists($authenticationField, $this->getAttributes())) {
                $this->authentication_field = $authenticationField;
            } else {
                throw new \InvalidArgumentException(sprintf('Your provider must return attribute "%s".', $authenticationField));
            }
        } else {
            $this->authentication_field = $authenticationField;
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        if ($this->isAuthenticated()) {
            if (\array_key_exists($this->authentication_field, $this->getAttributes())) {
                $attributes = $this->getAttributes();
                return $attributes[$this->authentication_field][0];
            } else {
                throw new \InvalidArgumentException(sprintf('Your provider must return attribute "%s".', $this->authentication_field));
            }
        }

        return null;
    }
}
