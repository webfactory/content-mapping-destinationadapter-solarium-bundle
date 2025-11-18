<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('contentmapping.destinationadapter.solarium.batch_size', 20);

    $services->set('contentmapping.destinationadapter.solarium.delete_from_index_command', \Webfactory\ContentMappingDestinationAdapterSolariumBundle\Command\DeleteFromIndexCommand::class)
        ->args([
            service('solarium.client'),
            service('logger'),
        ])
        ->tag('monolog.logger', ['channel' => 'contentmapping'])
        ->tag('console.command');

    $services->set(\Webfactory\ContentMapping\DestinationAdapter\Solarium\SolariumDestinationAdapter::class)
        ->args([
            service('solarium.client'),
            service('logger'),
            '%contentmapping.destinationadapter.solarium.batch_size%',
            '%kernel.debug%',
        ])
        ->factory([\Webfactory\ContentMappingDestinationAdapterSolariumBundle\SolariumDestinationAdapterFactory::class, 'create'])
        ->tag('monolog.logger', ['channel' => 'contentmapping']);

    $services->alias('contentmapping.destinationadapter.solarium', \Webfactory\ContentMapping\DestinationAdapter\Solarium\SolariumDestinationAdapter::class);
};
