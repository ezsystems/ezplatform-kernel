<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * Content locator gateway implementation using the DoctrineDatabase.
 */
class CriteriaConverter
{
    /**
     * Criterion handlers.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler[]
     */
    protected $handlers;

    /**
     * Construct from an optional array of Criterion handlers.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds handler.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler $handler
     */
    public function addHandler(CriterionHandler $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Generic converter of criteria into query fragments.
     *
     * @param array $languageSettings
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function convertCriteria(
        QueryBuilder $query,
        Criterion $criterion,
        array $languageSettings
    ) {
        foreach ($this->handlers as $handler) {
            if ($handler->accept($criterion)) {
                return $handler->handle($this, $query, $criterion, $languageSettings);
            }
        }

        throw new NotImplementedException(
            'No visitor available for: ' . get_class($criterion) . ' with operator ' . $criterion->operator
        );
    }
}

class_alias(CriteriaConverter::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter');
