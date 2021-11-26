<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for the Search Engine Indexer Service aimed to recreate Search Engine Index.
 * Each Search Engine has to extend it on its own.
 */
abstract class Indexer
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    protected $persistenceHandler;

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /** @var \Ibexa\Contracts\Core\Search\Handler */
    protected $searchHandler;

    public function __construct(
        LoggerInterface $logger,
        PersistenceHandler $persistenceHandler,
        Connection $connection,
        SearchHandler $searchHandler
    ) {
        $this->logger = $logger;
        $this->persistenceHandler = $persistenceHandler;
        $this->connection = $connection;
        $this->searchHandler = $searchHandler;
    }

    /**
     * Create search engine index.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $iterationCount
     * @param bool $commit commit changes after each iteration
     */
    abstract public function createSearchIndex(OutputInterface $output, $iterationCount, $commit);

    /**
     * Get DB Statement to fetch metadata about content objects to be indexed.
     *
     * @param array $fields fields to select
     */
    protected function getContentDbFieldsStmt(array $fields): Statement
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select($fields)
            ->from(ContentGateway::CONTENT_ITEM_TABLE)
            ->where($query->expr()->eq('status', ContentInfo::STATUS_PUBLISHED));

        return $query->execute();
    }
}

class_alias(Indexer::class, 'eZ\Publish\Core\Search\Common\Indexer');
