<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;

interface QueryFactoryInterface
{
    public function create(string $type, array $parameters = []): Query;
}

class_alias(QueryFactoryInterface::class, 'eZ\Publish\Core\Query\QueryFactoryInterface');
