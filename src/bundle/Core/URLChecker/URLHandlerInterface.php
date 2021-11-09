<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\URLChecker;

interface URLHandlerInterface
{
    /**
     * Validates given list of URLs.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URL[] $urls
     */
    public function validate(array $urls);
}

class_alias(URLHandlerInterface::class, 'eZ\Bundle\EzPublishCoreBundle\URLChecker\URLHandlerInterface');
