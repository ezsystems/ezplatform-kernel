<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Utils;

/**
 * Utility for logging deprecated error messages.
 */
interface DeprecationWarnerInterface
{
    /**
     * Logs a deprecation warning, as a E_USER_DEPRECATED message.
     *
     * @param string $message
     */
    public function log($message);
}

class_alias(DeprecationWarnerInterface::class, 'eZ\Publish\Core\Base\Utils\DeprecationWarnerInterface');
