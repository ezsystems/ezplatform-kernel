<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Pagination\Pagerfanta\AdapterFactory;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @internal
 */
interface SearchHitAdapterFactoryInterface
{
    public function createAdapter(Query $query, array $languageFilter = []): AdapterInterface;

    public function createFixedAdapter(Query $query, array $languageFilter = []): AdapterInterface;
}

class_alias(SearchHitAdapterFactoryInterface::class, 'eZ\Publish\Core\Pagination\Pagerfanta\AdapterFactory\SearchHitAdapterFactoryInterface');
