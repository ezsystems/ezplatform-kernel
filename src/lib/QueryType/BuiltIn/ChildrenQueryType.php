<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MatchNone;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ParentLocationId;

final class ChildrenQueryType extends AbstractLocationQueryType
{
    public static function getName(): string
    {
        return 'Children';
    }

    protected function getQueryFilter(array $parameters): Criterion
    {
        $location = $this->resolveLocation($parameters);

        if ($location === null) {
            return new MatchNone();
        }

        return new ParentLocationId($location->id);
    }
}

class_alias(ChildrenQueryType::class, 'eZ\Publish\Core\QueryType\BuiltIn\ChildrenQueryType');
