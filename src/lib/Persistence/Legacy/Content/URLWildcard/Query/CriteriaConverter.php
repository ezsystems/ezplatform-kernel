<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

final class CriteriaConverter
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler[] */
    private $handlers;

    /**
     * @param \Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler[] $handlers
     */
    public function __construct(iterable $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
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
