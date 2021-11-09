<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Regex as BaseRegex;

final class RegexMatcher extends BaseRegex
{
    public function getName()
    {
        throw new NotImplementedException(__METHOD__);
    }
}

class_alias(RegexMatcher::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\Stubs\RegexMatcher');
