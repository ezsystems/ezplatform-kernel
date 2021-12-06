<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\FieldType\BinaryBase;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

interface PathGeneratorInterface
{
    public function getStoragePathForField(Field $field, VersionInfo $versionInfo);
}

class_alias(PathGeneratorInterface::class, 'eZ\Publish\SPI\FieldType\BinaryBase\PathGeneratorInterface');
