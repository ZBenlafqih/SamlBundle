<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;

/**
 * @package    SamlBundle
 * @subpackage DependencyInjection\Security\Factory
 */
class SamlFactory extends AbstractFactory implements AuthenticatorFactoryInterface
{
    public function getPosition(): string
    {
        return 'pre_auth';
    }

    public function getKey(): string
    {
        return 'saml';
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        parent::addConfiguration($node);

        $builder = $node->children();
        $builder
            ->booleanNode('direct_entry')->defaultTrue()->end();
    }

    protected function getListenerId(): string
    {
        return 'saml.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, string $id, array $config, string $userProviderId): string
    {
        $authProviderId = 'security.authentication.provider.saml.'.$id;
        $container
            ->setDefinition($authProviderId, new ChildDefinition('saml.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId));

        return $authProviderId;
    }

    protected function createListener(ContainerBuilder $container, string $id, array $config, string $userProvider): string
    {
        $listenerId = $this->getListenerId();
        $listener = new ChildDefinition($listenerId);
        $listener->replaceArgument(8, $config);
        $listenerId .= '.'.$id;
        $container->setDefinition($listenerId, $listener);

        // Logout listener/handler
        $this->createLogoutHandler($container, $id, $config);

        return $listenerId;
    }

    protected function createLogoutHandler(ContainerBuilder $container, string $id, array $config): void
    {
        // Logout listener
        if ($container->hasDefinition('security.logout_listener.'.$id)) {
            $logoutListener = $container->getDefinition('security.logout_listener.'.$id);
            $samlListenerId = 'security.logout.handler.saml';

            // Add logout handler
            $container
                ->setDefinition($samlListenerId, new ChildDefinition('saml.security.http.logout'))
                ->replaceArgument(2, array_intersect_key($config, $this->options));
            $logoutListener->addMethodCall('addHandler', [new Reference($samlListenerId)]);
        }
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string
    {
        return $this->createListener($container, $firewallName, $config, $userProviderId);
    }

    public function getPriority(): int
    {
        return 0;
    }
}
