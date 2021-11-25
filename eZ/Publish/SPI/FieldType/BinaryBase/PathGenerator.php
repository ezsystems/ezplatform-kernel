<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\FieldType\BinaryBase;

use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

/**
 * @deprecated use \eZ\Publish\SPI\FieldType\BinaryBase\PathGeneratorInterface instead.
 */
abstract class PathGenerator implements PathGeneratorInterface
{
    abstract public function getStoragePathForField(Field $field, VersionInfo $versionInfo);
}
