<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Content type criterion handler.
 */
class ContentTypeIdentifier extends CriterionHandler
{
    /**
     * Content Type handler.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\Type\Handler
     */
    protected $contentTypeHandler;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        Connection $connection,
        ContentTypeHandler $contentTypeHandler,
        LoggerInterface $logger = null
    ) {
        parent::__construct($connection);

        $this->contentTypeHandler = $contentTypeHandler;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\ContentTypeIdentifier;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $idList = [];
        $invalidIdentifiers = [];

        foreach ($criterion->value as $identifier) {
            try {
                $idList[] = $this->contentTypeHandler->loadByIdentifier($identifier)->id;
            } catch (NotFoundException $e) {
                // Skip non-existing content types, but track for code below
                $invalidIdentifiers[] = $identifier;
            }
        }

        if (count($invalidIdentifiers) > 0) {
            $this->logger->warning(
                sprintf(
                    'Invalid content type identifiers provided for ContentTypeIdentifier criterion: %s',
                    implode(', ', $invalidIdentifiers)
                )
            );
        }

        if (count($idList) === 0) {
            return '1 = 0';
        }

        return $queryBuilder->expr()->in(
            'c.contentclass_id',
            $queryBuilder->createNamedParameter($idList, Connection::PARAM_INT_ARRAY)
        );
    }
}

class_alias(ContentTypeIdentifier::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\ContentTypeIdentifier');
