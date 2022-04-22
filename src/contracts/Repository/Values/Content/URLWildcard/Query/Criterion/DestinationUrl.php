<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

/**
 * Matches URLWildcards which contains the destination Url.
 */
final class DestinationUrl extends Matcher
{
    /**
     * String which needs to part of URLWildcard destination Url e.g. ez.no.
     *
     * @var string
     */
    public $destinationUrl;

    public function __construct(string $destinationUrl)
    {
        if ($destinationUrl === '') {
            throw new \InvalidArgumentException('URLWildcard destination url cannot be empty.');
        }

        $this->destinationUrl = $destinationUrl;
    }
}
