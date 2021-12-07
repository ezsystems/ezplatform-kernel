<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\FieldType\Image;

/**
 * Default, IO-independent, implementation of image variation (alias) cleaner.
 *
 * It should be overridden by IO/filesystem and image manipulation specific integration,
 * on a Bundle level.
 *
 * @internal for internal use by Repository Image Field Type External Storage
 */
final class NullAliasCleaner implements AliasCleanerInterface
{
    public function removeAliases($originalPath): void
    {
        // Nothing to do
    }
}

class_alias(NullAliasCleaner::class, 'eZ\Publish\Core\FieldType\Image\NullAliasCleaner');
