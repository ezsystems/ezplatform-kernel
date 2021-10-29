<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\IO;

/**
 * @internal
 */
interface FilePathNormalizerInterface
{
    public function normalizePath(string $filePath): string;
}

class_alias(FilePathNormalizerInterface::class, 'eZ\Publish\Core\IO\FilePathNormalizerInterface');
