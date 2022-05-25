<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

/**
 * Matches URLWildcards based on type.
 */
final class Type extends Matcher
{
    /** @var bool */
    public $forward;

    public function __construct(bool $forward)
    {
        $this->forward = $forward;
    }
}
