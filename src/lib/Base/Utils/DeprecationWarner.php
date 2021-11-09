<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Utils;

class DeprecationWarner implements DeprecationWarnerInterface
{
    public function log($message)
    {
        @trigger_error($message, E_USER_DEPRECATED);
    }
}

class_alias(DeprecationWarner::class, 'eZ\Publish\Core\Base\Utils\DeprecationWarner');
