<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

class CriteriaConverter
{
    /**
     * Criterion handlers.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler[]
     */
    protected $handlers;

    /**
     * Construct from an optional array of Criterion handlers.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds handler.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler $handler
     */
    public function addHandler(CriterionHandler $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Generic converter of criteria into query fragments.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotImplementedException if Criterion is not applicable to its target
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     */
    public function convertCriteria(QueryBuilder $queryBuilder, Criterion $criterion)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->accept($criterion)) {
                return $handler->handle($this, $queryBuilder, $criterion);
            }
        }

        throw new NotImplementedException(
            'No visitor available for: ' . get_class($criterion)
        );
    }
}
