<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * A criterion that matches content that is sibling to the given Location.
 */
class Sibling extends CompositeCriterion implements FilteringCriterion
{
    public function __construct(int $locationId, int $parentLocationId)
    {
        $criteria = new LogicalAnd([
            new ParentLocationId($parentLocationId),
            new LogicalNot(
                new LocationId($locationId)
            ),
        ]);

        parent::__construct($criteria);
    }

    public static function fromLocation(Location $location): self
    {
        return new self($location->id, $location->parentLocationId);
    }
}

class_alias(Sibling::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\Sibling');
