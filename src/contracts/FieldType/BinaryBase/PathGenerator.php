<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\FieldType\BinaryBase;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

/**
 * @deprecated use \Ibexa\Contracts\Core\FieldType\BinaryBase\PathGeneratorInterface instead.
 */
abstract class PathGenerator implements PathGeneratorInterface
{
    abstract public function getStoragePathForField(Field $field, VersionInfo $versionInfo);
}

class_alias(PathGenerator::class, 'eZ\Publish\SPI\FieldType\BinaryBase\PathGenerator');
