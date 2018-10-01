# content-mapping-destinationadapter-solarium-bundle #

Symfony bundle for [webfactory/content-mapping-destinationadapter-solarium](https://github.com/webfactory/content-mapping-destinationadapter-solarium),
easing your synchronization life via simple configuration, a services and a console command.


## Installation ##

Install the package via composer:

    composer require webfactory/content-mapping-destinationadapter-solarium-bundle

and enable two bundles in your app kernel:
    
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    // ...
    $bundles[] = new Webfactory\ContentMappingDestinationAdapterSolariumBundle\WebfactoryContentMappingDestinationAdapterSolariumBundle();
    $bundles[] = new Nelmio\SolariumBundle\NelmioSolariumBundle();
    // ...
}
```

and finally, configure the endpoint for the Solarium client (currently, only the default client with the default
endpoint is supported): 

```yml
// app/config.yml

nelmio_solarium:
    endpoints:
        default:
            dsn: http://localhost:8983/solr/your-core
```


## Usage ##

The bundle provides a service called `contentmapping.destinationadapter.solarium` which you can use to define a
Synchronizer service inside the [webfactory/content-mapping](https://github.com/webfactory/content-mapping) mini
framework:

```xml
<!-- Resources/config/services.xml -->

<!-- Synchronizer for MyEntity --->
<service class="Webfactory\ContentMapping\Synchronizer">
    <!-- SourceAdapter -->
    <argument type="service">
        <service class="Webfactory\ContentMapping\SourceAdapter\Doctrine\GenericDoctrineSourceAdapter">
            <!-- Doctrine Repository -->
            <argument type="service">
                <service class="MyVendor\MyBundle\Entity\MyEntityRepository" factory-service="doctrine.orm.entity_manager" factory-method="getRepository">
                    <argument>MyVendorMyEntityBundle:MyEntity</argument>
                </service>
            </argument>
            <!-- Name of the repository method to query -->
            <argument type="string">findForSolrIndex</argument>
        </service>
    </argument>

    <!-- Mapper -->
    <argument type="service">
        <service class="MyVendor\MyBundle\ContentMapping\MyEntityMapper" />
    </argument>

    <!-- DestinationAdapter -->
    <argument type="service" id="contentmapping.destinationadapter.solarium"/>

    <!-- PSR3-logger -->
    <argument type="service" id="logger" />
    <tag name="monolog.logger" channel="solr" />
    
    <!-- Tag to mark the service as a Synchronizer -->
    <tag name="contentmapping.synchronizer" objectclass="\JugendFuerEuropa\Bundle\JugendInAktionBundle\Entity\Mitarbeiter" />
</service>

<!-- other Synchronizers --->
```

As a bonus, this bundle provides you with a console command for deleting data from the solr index matching a query:

    app/bin content-mapping:solarium:delete-from-index [query=*:*]


## Credits, Copyright and License ##

This project was started at webfactory GmbH, Bonn.

- <http://www.webfactory.de>
- <http://twitter.com/webfactory>

Copyright 2015 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
