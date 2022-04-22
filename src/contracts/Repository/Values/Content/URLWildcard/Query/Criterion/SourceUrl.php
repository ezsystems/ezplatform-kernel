<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

/**
 * Matches URLWildcards which contains the source Url.
 */
final class SourceUrl extends Matcher
{
    /**
     * String which needs to part of URLWildcard source Url e.g. ez.no.
     *
     * @var string
     */
    public $sourceUrl;

    public function __construct(string $sourceUrl)
    {
        if ($sourceUrl === '') {
            throw new \InvalidArgumentException('URLWildcard source url cannot be empty.');
        }

        $this->sourceUrl = $sourceUrl;
    }
}
