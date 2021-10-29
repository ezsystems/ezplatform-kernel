<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;

class MatchAll extends Matcher
{
}

class_alias(MatchAll::class, 'eZ\Publish\API\Repository\Values\URL\Query\Criterion\MatchAll');
