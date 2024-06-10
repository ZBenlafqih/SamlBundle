<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use PDias\SamlBundle\Saml\SamlAuth;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlUserProvider implements UserProviderInterface
{
    protected SamlAuth $samlAuth;
    protected array $attributes;

    public function __construct(SamlAuth $samlAuth)
    {
        $this->samlAuth = $samlAuth;
        $this->attributes = $this->samlAuth->getAttributes();
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if ($this->samlAuth->isAuthenticated()) {
            return new SamlUser($this->samlAuth->getUsername(), ['ROLE_USER'], $this->attributes);
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $identifier));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SamlUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return $class === SamlUser::class;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
