<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\IO\FilePathNormalizer;

use Ibexa\Core\IO\FilePathNormalizerInterface;
use League\Flysystem\Util;

final class Flysystem implements FilePathNormalizerInterface
{
    public function normalizePath(string $filePath): string
    {
        return Util::normalizePath($filePath);
    }
}

class_alias(Flysystem::class, 'eZ\Publish\Core\IO\FilePathNormalizer\Flysystem');
