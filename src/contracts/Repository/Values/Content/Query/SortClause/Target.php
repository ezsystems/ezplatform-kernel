<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

/**
 * Struct that stores extra target informations for a SortClause object.
 */
abstract class Target
{
}

class_alias(Target::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\Target');
