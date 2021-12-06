<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\RelationList\Item;

use Ibexa\Contracts\Core\Repository\Lists\UnauthorizedListItem;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList\RelationListItemInterface;

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

class_alias(UnauthorizedRelationListItem::class, 'eZ\Publish\API\Repository\Values\Content\RelationList\Item\UnauthorizedRelationListItem');
