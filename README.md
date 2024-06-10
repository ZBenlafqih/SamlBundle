# Kurollo-SamlBundle for Symfony >= 5.4 #


The ***SamlBundle*** adds support for [***SAML 2.0 Service Provider***](https://simplesamlphp.org/ "simpleSAMLphp Web Page") in ***Symfony >= 5.4***. It provides security listener that can be configured to authenticate users against one or more ***SAML Identity Providers***.


- [SimpleSAMLphp Installation and Configuration](https://simplesamlphp.org/docs/stable/simplesamlphp-install "Installation and Configuration");

## License ##

This bundle is under the MIT license. [***See the complete license in the bundle***](https://github.com/pdias/SamlBundle/blob/master/Resources/meta/LICENSE "SamlBundle License"):

    Resources/meta/LICENSE

## Install ##

    composer require alteis/Saml-Bundle

## Configuration ##
 add in services.yml


        ....

        PDias\SamlBundle\Controller\SecurityController:
        tags: [ 'controller.service_arguments' ]
        calls:
            - ['setContainer', ['@service_container', ContainerInterface]]

        ...

 add in packages security.yml

    secuirty:
        ...

        providres:

        ....
            samlservice:
                id: saml.service.user.provider
        ...

        firewall:
            saml_secured:
                pattern: ^/
                saml:
                    provider: samlservice
                    login_path: /login-saml
                    check_path: /login-check-saml
                    default_target_path: /
                    always_use_default_target_path: true
                logout:
                    path:   /logout-saml
                    target: /
        ....


## Documentation ##

[Getting Started With SimpleSamlPhp](https://simplesamlphp.org/docs/stable/index.html)




Credits
------

Thanks to ***Esmeralda Câmara*** from [FCCN](http://www.fccn.pt "Fundação para a Ciência e a Tecnologia"). 
