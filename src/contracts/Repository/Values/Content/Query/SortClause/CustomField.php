<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Target\CustomFieldTarget;

/**
 * Sorts search results by raw search index field.
 */
final class CustomField extends SortClause
{
    public function __construct(string $field, string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('custom_field', $sortDirection, new CustomFieldTarget($field));
    }
}

class_alias(CustomField::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\CustomField');
