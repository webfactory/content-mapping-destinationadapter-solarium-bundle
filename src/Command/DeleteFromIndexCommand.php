<?php

namespace Webfactory\ContentMappingDestinationAdapterSolariumBundle\Command;

use Psr\Log\LoggerInterface;
use Solarium\Client;
use Solarium\QueryType\Update\Result;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Symfony Console Command for deleting data from a Solr Index.
 */
class DeleteFromIndexCommand extends Command
{
    /**
     * @var Client
     */
    private $solrClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Client          $solrClient
     * @param LoggerInterface $logger
     */
    public function __construct(Client $solrClient, LoggerInterface $logger)
    {
        parent::__construct();
        $this->solrClient = $solrClient;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('content-mapping:solarium:delete-from-index')
             ->setDescription('Delete data from the index');
        $this->addOption(
            'query',
            '',
            InputOption::VALUE_OPTIONAL,
            'Query expression to use for deletion (defaults to *:*)'
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getOption('query') ?: '*:*';

        $this->logger->info('Starting delete query "{query}"', array('query' => $query));

        $updateCommand = $this->solrClient->createUpdate();
        $updateCommand->addDeleteQuery($query);
        $updateCommand->addCommit();

        /** @var Result $result */
        $result = $this->solrClient->execute($updateCommand);
        $this->logger->info(
            'Finished delete query "{query}", status: {status}, duration: {duration}',
            array(
                'query' => $query,
                'status' => $result->getStatus(),
                'duration' => $result->getQueryTime(),
            )
        );
    }
}
