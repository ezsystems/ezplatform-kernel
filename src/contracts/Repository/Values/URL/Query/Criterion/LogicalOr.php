<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;

class LogicalOr extends LogicalOperator
{
}

class_alias(LogicalOr::class, 'eZ\Publish\API\Repository\Values\URL\Query\Criterion\LogicalOr');
