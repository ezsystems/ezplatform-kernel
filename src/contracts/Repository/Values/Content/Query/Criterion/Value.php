<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * Struct that stores extra value information for a Criterion object.
 */
abstract class Value
{
}

class_alias(Value::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\Value');
