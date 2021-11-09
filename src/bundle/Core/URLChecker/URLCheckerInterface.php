<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\URLChecker;

use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;

interface URLCheckerInterface
{
    /**
     * Checks URLs returned by given query.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URLQuery $query
     */
    public function check(URLQuery $query);
}

class_alias(URLCheckerInterface::class, 'eZ\Bundle\EzPublishCoreBundle\URLChecker\URLCheckerInterface');
