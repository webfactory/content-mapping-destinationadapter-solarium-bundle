<?php

namespace Webfactory\ContentMappingDestinationAdapterSolariumBundle;

use Psr\Log\LoggerInterface;
use Solarium\Client;
use Webfactory\ContentMapping\DestinationAdapter\Solarium\SolariumDestinationAdapter;

class SolariumDestinationAdapterFactory
{
    public static function create(Client $solrClient, LoggerInterface $logger, int $batchSize = 20, bool $debug = false): SolariumDestinationAdapter
    {
        if ($debug) {
            $logger->warning(
                "Heads up: kernel.debug=true, so nelmio/solarium-bundle is collecting profiling information. ".
                "This may cause excessive memory usage as data will not be freed after being sent to Solr."
            );
        }

        return new SolariumDestinationAdapter($solrClient, $logger, $batchSize);
    }
}
