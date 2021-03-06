<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content\RelationList;

use eZ\Publish\API\Repository\Values\Content\Relation;

interface RelationListItemInterface
{
    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Relation|null
     */
    public function getRelation(): ?Relation;

    /**
     * @return bool
     */
    public function hasRelation(): bool;
}
