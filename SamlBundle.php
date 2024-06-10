<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle;

use PDias\SamlBundle\DependencyInjection\Security\Factory\SamlFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;

/**
 * @author: Paulo Dias <dias.paulo@gmail.com>
 */
class SamlBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        if ($extension instanceof SecurityExtension) {
            $extension->addAuthenticatorFactory(new SamlFactory());
        }
    }
}