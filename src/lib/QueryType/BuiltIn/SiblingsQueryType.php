<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MatchNone;

final class SiblingsQueryType extends AbstractLocationQueryType
{
    public static function getName(): string
    {
        return 'Siblings';
    }

    protected function getQueryFilter(array $parameters): Criterion
    {
        $location = $this->resolveLocation($parameters);

        if ($location === null) {
            return new MatchNone();
        }

        return Criterion\Sibling::fromLocation($location);
    }
}

class_alias(SiblingsQueryType::class, 'eZ\Publish\Core\QueryType\BuiltIn\SiblingsQueryType');
