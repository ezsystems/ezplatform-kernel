<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content\RelationList\Item;

use eZ\Publish\API\Repository\Lists\UnauthorizedListItem;
use eZ\Publish\API\Repository\Values\Content\Relation;
use eZ\Publish\API\Repository\Values\Content\RelationList\RelationListItemInterface;

/**
 * Item of relation list.
 */
final class UnauthorizedRelationListItem extends UnauthorizedListItem implements RelationListItemInterface
{
    public function getRelation(): ?Relation
    {
        return null;
    }

    public function hasRelation(): bool
    {
        return false;
    }
}
