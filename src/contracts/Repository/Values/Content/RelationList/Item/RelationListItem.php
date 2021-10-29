<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\RelationList\Item;

use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList\RelationListItemInterface;

/**
 * Item of relation list.
 */
class RelationListItem implements RelationListItemInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Relation */
    private $relation;

    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    public function getRelation(): ?Relation
    {
        return $this->relation;
    }

    public function hasRelation(): bool
    {
        return true;
    }
}

class_alias(RelationListItem::class, 'eZ\Publish\API\Repository\Values\Content\RelationList\Item\RelationListItem');
