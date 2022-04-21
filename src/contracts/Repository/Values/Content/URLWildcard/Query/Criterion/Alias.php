<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

/**
 * Matches URLWildcards which contains the alias.
 */
class Alias extends Matcher
{
    /**
     * String which needs to part of URLWildcard alias e.g. ez.no.
     *
     * @var string
     */
    public $alias;

    public function __construct(string $alias)
    {
        if ($alias === '') {
            throw new \InvalidArgumentException('URL alias cannot be empty.');
        }

        $this->alias = $alias;
    }
}
