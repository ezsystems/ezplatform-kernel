<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Ibexa\Contracts\Core\Persistence\Content\Relation;

$relation = new Relation();
$relation->id = 1;
$relation->sourceContentId = 1;
$relation->sourceContentVersionNo = 1;
$relation->type = 1;
$relation->destinationContentId = 2;

return [1 => $relation];
