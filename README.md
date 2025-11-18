# content-mapping-destinationadapter-solarium-bundle 

Symfony bundle for [webfactory/content-mapping-destinationadapter-solarium](https://github.com/webfactory/content-mapping-destinationadapter-solarium),
easing your synchronization life via simple configuration, a services and a console command.

## Installation 

Install the package via composer:

    composer require webfactory/content-mapping-destinationadapter-solarium-bundle

and register two bundles in your Symfony Kernel:

* `Webfactory\ContentMappingDestinationAdapterSolariumBundle\WebfactoryContentMappingDestinationAdapterSolariumBundle`
* `Nelmio\SolariumBundle\NelmioSolariumBundle`

Also, configure the endpoint for the Solarium client. Currently, only the default client with the default endpoint is supported. 

```yml
// app/config.yml

nelmio_solarium:
    endpoints:
        default:
            host: localhost
            port: 8983
            core: your-core
```

## Usage 

The bundle provides a service called `contentmapping.destinationadapter.solarium` which you can use to define a
Synchronizer service inside the [webfactory/content-mapping](https://github.com/webfactory/content-mapping) mini
framework:

```php
<?php # Resources/config/services.php

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('my_entity_synchronizer', \Webfactory\ContentMapping\Synchronizer::class)
        ->args([
            inline_service('Webfactory\ContentMapping\SourceAdapter\Doctrine\GenericDoctrineSourceAdapter')
                ->args([
                    inline_service('MyVendor\MyBundle\Entity\MyEntityRepository'),
                    'findForSolrIndex',
                ]),
            inline_service('MyVendor\MyBundle\ContentMapping\MyEntityMapper'),
            service('contentmapping.destinationadapter.solarium'),
            service('logger'),
        ])
        ->tag('monolog.logger', ['channel' => 'solr'])
        ->tag('contentmapping.synchronizer', ['objectclass' => 'my-object-class']);
        
        // other synchronizers here
};
```

As a bonus, this bundle provides you with a console command for deleting data from the solr index matching a query:

    app/bin content-mapping:solarium:delete-from-index [query=*:*]

## Credits, Copyright and License 

This project was started at webfactory GmbH, Bonn.

- <https://www.webfactory.de>

Copyright 2015-2025 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
