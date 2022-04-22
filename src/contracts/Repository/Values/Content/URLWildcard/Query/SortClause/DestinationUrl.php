<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\SortClause;

final class DestinationUrl extends SortClause
{
    public function __construct(string $sortDirection = self::SORT_ASC)
    {
        parent::__construct('destinationUrl', $sortDirection);
    }
}
