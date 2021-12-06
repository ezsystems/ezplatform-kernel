<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\QueryType\QueryTypeRegistry;

final class QueryFactory implements QueryFactoryInterface
{
    /** @var \Ibexa\Core\QueryType\QueryTypeRegistry */
    private $queryTypeRegistry;

    public function __construct(QueryTypeRegistry $queryTypeRegistry)
    {
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    public function create(string $type, array $parameters = []): Query
    {
        return $this->queryTypeRegistry->getQueryType($type)->getQuery($parameters);
    }
}

class_alias(QueryFactory::class, 'eZ\Publish\Core\Query\QueryFactory');
