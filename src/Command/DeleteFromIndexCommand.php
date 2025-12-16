<?php

namespace Webfactory\ContentMappingDestinationAdapterSolariumBundle\Command;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
    public function __construct(
        private readonly Client $solrClient,
        private readonly LoggerInterface $logger = new NullLogger()
    ) {
        parent::__construct();
    }

    protected function configure(): void
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

    protected function execute(InputInterface $input, OutputInterface $output): int
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

        return 0;
    }
}
