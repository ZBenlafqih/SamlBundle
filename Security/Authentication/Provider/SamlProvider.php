<?php
/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use PDias\SamlBundle\Security\Authentication\Token\SamlUserToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlProvider implements AuthenticationProviderInterface
{
    private UserProviderInterface $userProvider;
    private string $cacheDir;

    public function __construct(UserProviderInterface $userProvider, string $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir     = $cacheDir;
    }

    public function authenticate(TokenInterface $token): ?TokenInterface
    {
        if (!$this->supports($token)) { 
            return null;
        }

        if ($token instanceof SamlUserToken) {
            $user = $this->userProvider->loadUserByIdentifier($token->getUserIdentifier());

            if ($user instanceof UserInterface) {
                $authenticatedToken = new SamlUserToken($user->getRoles());
                $authenticatedToken->setUser($user);
                $authenticatedToken->setAuthenticated(true);
                $authenticatedToken->setAttributes($this->userProvider->getAttributes());
                $authenticatedToken->setDirectEntry($token->getDirectEntry());

                return $authenticatedToken;
            }
        }

        throw new AuthenticationException('The SAML authentication failed.');
    }

    public function supports(TokenInterface $token): bool
    {
        return $token instanceof SamlUserToken;
    }
}